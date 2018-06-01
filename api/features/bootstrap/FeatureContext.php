<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behatch\Context\RestContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @var JWTManager
     */
    private $jwtManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $manager;


    /**
     * @var RestContext
     */
    private $restContext;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param ManagerRegistry $doctrine
     * @param JWTManager $jwtManager
     */
    public function __construct(ManagerRegistry $doctrine, JWTManager $jwtManager, UserPasswordEncoderInterface $encoder)
    {
        $this->doctrine = $doctrine;
        $this->manager = $this->doctrine->getManager();
        $this->schemaTool = new SchemaTool($this->manager);
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
        $this->jwtManager = $jwtManager;
        $this->encoder = $encoder;
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
     * @param BeforeScenarioScope $scope
     * @param UserPasswordEncoderInterface $encoder
     */
    public function login(BeforeScenarioScope $scope)
    {
        $user = $this->doctrine->getRepository(\App\Entity\User::class)->findOneBy(['username' => 'admin']);

        if (!isset($user)) {
            $user = new \App\Entity\User();
            $user->setUsername('admin');
            $user->setEmail('admin@example.com');
            $encoded = $this->encoder->encodePassword($user, 'admin');
            $user->setPassword($encoded);
            $this->manager->persist($user);
            $this->manager->flush();
        }

        $token = $this->jwtManager->create($user);

        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
        $this->restContext->iAddHeaderEqualTo('Authorization', 'Bearer ' . $token);
    }

//    /**
//     * @afterScenario
//     * @logout
//     */
//    public function logout()
//    {
//        $this->restContext->iAddHeaderEqualTo('Authorization', '');
//    }
}
