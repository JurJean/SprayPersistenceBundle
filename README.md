Spray\PersistenceBundle
=======================

A Symfony2 bundle that enhances Doctrine2 repository functionality.

[![Build Status](https://secure.travis-ci.org/JurJean/SprayPersistenceBundle.png?branch=master)](http://travis-ci.org/JurJean/SprayPersistenceBundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/JurJean/SprayPersistenceBundle/badges/quality-score.png?s=f3038d9bc0af391724f4ae27f3132dcae6520302)](https://scrutinizer-ci.com/g/JurJean/SprayPersistenceBundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/JurJean/SprayPersistenceBundle/badges/coverage.png?s=d74fc08c3020974dafd5708d25ae6b87f731d13b)](https://scrutinizer-ci.com/g/JurJean/SprayPersistenceBundle/)

Introduction
------------

This bundle provides a way to query your objects in an abstract manner:
```php
      $articles->filter('currentlyPublished');
      $articles->filter('writtenBy', new Author('Buster'));
      $articles->filter('ascending');
```
It has a common API:

      $articleCount = count($articles);
      foreach ($articles as $article) {
          
      }

Allows easy pagination:

      foreach ($articles->paginate(1) as $article) {
          
      }

And can be used standalone as well! You don't need symfony, you can integrate it
in any framework of choice (however Doctrine is a requirement).


Installation
------------

Require "jurjean/spray-persistence-bundle" in your composer.json:

    {
        "require": {
            "jurjean/spray-persistence-bundle": "2.2.*@dev"
        }
    }

Register SprayPersistenceBundle in your AppKernel:

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Spray\PersistenceBundle\SprayPersistenceBundle(),
            );
            return $bundles;
        }
    }


The problem
-----------

Doctrine2 provides a nice API that lets you query for entities through a
repository. However the repository pattern is not very DRY. 

If you want to do queries like described above, you could end up with a
repository like so:

    class ArticleRepository extends Repository
    {
        public function findCurrentlyPublished($order)
        {
            
        }
        
        public function findCurrentlyPublishedAndWrittenBy(Author $author, $order)
        {
            
        }
    }

As you can imagine, the only way to add more conditions is by duplication.
That's where the RepositoryFilter comes in.

Entity filters
--------------


Prioritized entity filters
--------------------------

You may want a filter to be prioritized. To do so you must implement the
PrioritizedFilterInterface:

    use Doctrine\ORM\QueryBuilder;
    use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;
    use Spray\PersistenceBundle\EntityFilter\PrioritizedFilterInterface;

    class First implements EntityFilterInterface, PrioritizedFilterInterface
    {
        public function filter(QueryBuilder $queryBuilder, $options = array())
        {
            
        }

        public function getName()
        {
            return 'first';
        }

        public function getPriority()
        {
            return 100;
        }
    }

    class Last implements EntityFilterInterface, PrioritizedFilterInterface
    {
        public function filter(QueryBuilder $queryBuilder, $options = array())
        {
            
        }

        public function getName()
        {
            return 'last';
        }

        public function getPriority()
        {
            return -100;
        }
    }

No matter in which order you add these filters, the order of execution
still would be first and then last.

    $repository->filter('last'); // Added at priority level -100
    $repository->filter('first'); // Added at priority level 100, before 'last'


Conflicting entity filters
--------------------------

If you have filters that may conflict with each other (for instance if they
add a where statement on the same column) you can implement the
ConflictingFilterInterface:

    use Doctrine\ORM\QueryBuilder;
    use Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface;

    class ArticlesConflictingWith implements ConflictingFilterInterface
    {
        public function filter(QueryBuilder $queryBuilder, $options = array())
        {
            
        }

        public function getName()
        {
            return 'conflictingWith';
        }

        public function getConflictingFilters()
        {
            return array('another');
        }
    }

If 'another' exists in the repository filter scope, it will be removed if
ArticlesConflictingWith is added.

    $articles->filter('another');
    $articles->filter('conflictingWith'); // This is now the only filter

Filter registry
---------------

The filter registry is used to provide _repository filters_ with available
_entity filters_. You can either build them up programmatically, or create
registry classes yourself. After that you need to inject the registry into the
repository filter.

    class ArticleFilters extends FilterRegistry
    {
        public function __construct()
        {
            $this->add(new ArticlesPublishedSince());
            $this->add(new ArticlesWrittenBy());
        }
    }

    $articles = new RepositoryFilter($entityManager->getRepository('Article'));
    $articles->setFilterLocator(new ArticleFilters());

    $articles->filter('publishedSince');
    $articles->filter('writtenBy', new Author('Buster'));


Symfony integration
-------------------

You can configure your repository filters easily by extending the parent di
container definition _spray_persistence.repository_filter_, and providing it
with an Entity name as argument.

    <container>
        <services>
            <service id="bundle.articles"
                     parent="spray_persistence.repository_filter">
                <argument>Bundle\Entity\Article</argument>
            </service>
        </services>
    </container>

Filters are added by tagging them with name _spray_persistence.entity_filter_.
You can either set them up globally (for all repositories) or locally (for one
repository). You make them local by adding _repository_ as a tag option.

    <container>
        <services>
            <service id="bundle.articles"
                     parent="spray_persistence.repository_filter">
                <argument>Bundle\Entity\Article</argument>
            </service>
            <service id="bundle.filter.global"
                     class="Bundle\EntityFilter\Global">
                <tag name="spray_persistence.entity_filter" />
            </service>
            <service id="bundle.filter.local"
                     class="Bundle\EntityFilter\Local">
                <tag name="spray_persistence.entity_filter" repository="bundle.articles" />
            </service>
        </services>
    </container>

Examples
--------

For more examples please have a look at the
[integration tests for this project](test/Spray/PersistenceBundle/Integration).
