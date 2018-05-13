<?php

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var array
     */
    private $classes;

    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $manager;

    private $restContext;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param ManagerRegistry $doctrine
     * @param $jwtManager
     */
    public function __construct(ManagerRegistry $doctrine, JWTTokenManagerInterface $jwtManager)
    {
        $this->doctrine = $doctrine;
        $this->manager = $this->doctrine->getManager();
        $this->schemaTool = new SchemaTool($this->manager);
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
        $this->jwtManager = $jwtManager;
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createDatabase()
    {
        $this->schemaTool->dropSchema($this->classes);
        $this->schemaTool->createSchema($this->classes);
    }

    /**
     * @AfterScenario @dropSchema
     */
    public function dropDatabase()
    {
        $this->schemaTool->dropSchema($this->classes);
    }

    /**
     * @BeforeScenario
     * @login
     *
     * @see https://symfony.com/doc/current/security/entity_provider.html#creating-your-first-user
     */
    public function login(Behat\Behat\Hook\Scope\BeforeScenarioScope $scope)
    {
        $user = $this->doctrine->getRepository(\App\Entity\User::class)->findOneBy(['username' => 'admin']);

        if (!isset($user)) {
            $user = new \App\Entity\User();
            $user->setUsername('admin');
            $user->setPassword('$2a$08$jHZj/wJfcVKlIwr5AvR78euJxYK7Ku5kURNhNx.7.CSIJ3Pq6LEPC');
            $user->setEmail('admin@example.com');
            $this->manager->persist($user);
            $this->manager->flush();
        }

        $token = $this->jwtManager->create($user);

        $this->restContext = $scope->getEnvironment()->getContext(\Behatch\Context\RestContext::class);
        $this->restContext->iAddHeaderEqualTo('Authorization', 'Bearer '.$token);
    }

    /**
     * @afterScenario
     * @logout
     */
    public function logout()
    {
        $this->restContext->iAddHeaderEqualTo('Authorization', '');
    }
}
