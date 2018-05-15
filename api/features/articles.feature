# features/articles.feature
Feature: Manage articles and their comments
  In order to manage articles and their comments
  As a client software developer
  I need to be able to retrieve, create, update and delete them through the API.

  # the "@createSchema" annotation provided by API Platform creates a temporary SQLite database for testing the API
  @createSchema
  @login
  Scenario: Create a article
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/articles" with body:
    """
    {
      "title": "Persistence in PHP with the Doctrine ORM",
      "body": "Persistence in PHP with the Doctrine ORM",
      "author": "Kévin Dunglas",
      "publicationDate": "2013-12-01"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be equal to:
    """
    {
      "@context": "/contexts/Article",
      "@id": "/articles/1",
      "@type": "Article",
      "id": 1,
      "title": "Persistence in PHP with the Doctrine ORM",
      "body": "Persistence in PHP with the Doctrine ORM",
      "author": "K\u00e9vin Dunglas",
      "publicationDate": "2013-12-01T00:00:00+00:00",
      "comments": []
    }
    """

  Scenario: Retrieve the article list
    When I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/articles"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be equal to:
    """
    {
      "@context": "/contexts/Article",
      "@id": "/articles",
      "@type": "hydra:Collection",
      "hydra:member": [
        {
          "@id": "/articles/1",
          "@type": "Article",
          "id": 1,
          "title": "Persistence in PHP with the Doctrine ORM",
          "body": "Persistence in PHP with the Doctrine ORM",
          "author": "K\u00e9vin Dunglas",
          "publicationDate": "2013-12-01T00:00:00+00:00",
          "comments": []
        }
      ],
      "hydra:totalItems": 1
    }
    """

  Scenario: Throw errors when a post is invalid
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/articles" with body:
    """
    {
      "title": "",
      "body": "this is a body",
      "author": "Me!",
      "publicationDate": "2016-01-01"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be equal to:
    """
    {
      "@context": "/contexts/ConstraintViolationList",
      "@type": "ConstraintViolationList",
      "hydra:title": "An error occurred",
      "hydra:description": "title: This value should not be blank.",
      "violations": [
        {
          "propertyPath": "title",
          "message": "This value should not be blank."
        }
      ]
    }
    """

  # The "@dropSchema" annotation must be added on the last scenario of the feature file to drop the temporary SQLite database

  Scenario: Add a comment
    When I add "Content-Type" header equal to "application/ld+json"
    When I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/comments" with body:
    """
    {
      "article": "articles/1",
      "body": "Must have!",
      "author": "Foo Bar",
      "publicationDate": "2016-01-01"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be equal to:
    """
    {
        "@context": "/contexts/Comment",
        "@id": "/comments/1",
        "@type": "Comment",
        "id": 2,
        "article": "/articles/1",
        "body": "Must have!",
        "author": "Foo Bar",
        "publicationDate": "2016-01-01T00:00:00+00:00"
    }
    """
  @logout
  @dropSchema
