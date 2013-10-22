Spray\PersistenceBundle
=======================

A Symfony2 bundle that enhances Doctrine2 repository functionality.

[![Build Status](https://secure.travis-ci.org/JurJean/SprayPersistenceBundle.png?branch=master)](http://travis-ci.org/JurJean/SprayPersistenceBundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/JurJean/SprayPersistenceBundle/badges/quality-score.png?s=f3038d9bc0af391724f4ae27f3132dcae6520302)](https://scrutinizer-ci.com/g/JurJean/SprayPersistenceBundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/JurJean/SprayPersistenceBundle/badges/coverage.png?s=d74fc08c3020974dafd5708d25ae6b87f731d13b)](https://scrutinizer-ci.com/g/JurJean/SprayPersistenceBundle/)


The problem
-----------

Doctrine2 provides a nice api that lets you query for your entities through the
Repository. However the implemented Repository pattern is not very DRY. If you
have more complicated queries to execute the suggested solution is to override
the default Repository class and add methods as follows:

    class MyRepository extends Repository
    {
        public function findPublished()
        {
            // Perform query
        }
    }

When your application grows larger you might need to find published entities
before a specific date:

    class MyRepository extends Repository
    {
        public function findPublished()
        {
            // Perform query
        }

        public function findPublishedBeforeDate(DateTime $date)
        {
            // Same logic as findPublished()
            // Perform before date query
        }
    }

Now you need a published entity, before a specific date, within a radius of your
location.

At this moment you'll create more and more duplicate code. That's where the
FilterableEntityRepository comes in. The above logic can be rewritten as:

    class EntityPublished implements EntityFilterInterface
    {
        public function filter(QueryBuilder $qb)
        {
            // Perform query
        }
    }

    class EntityBeforeDate extends AbstractBeforeFilter
    {
        protected $propertyName = 'foo';
    }

    class EntityWithinRadius implements EntityFilterInterface
    {
        public function filter(QueryBuilder $qb)
        {
            // Perform query
        }
    }

    $repository = new FilterableEntityRepository();
    $repository->filter(new EntityPublished());
    $repository->filter(new EntityBeforeDate());
    $repository->filter(new EntityWithinRadius());

Prioritized filters
-------------------

You may want a filter to be prioritized. To do so you must implement the
PrioritizedFilterInterface:

    use Doctrine\ORM\QueryBuilder;
    use Spray\PersistenceBundle\EntityFilter\PrioritizedFilterInterface;

    class PrioritizedFilter implements PrioritizedFilterInterface
    {
        public function filter(QueryBuilder $qb)
        {
            // Do stuff
        }

        public function getName()
        {
            return 'prioritized_filter';
        }

        public function getPriority()
        {
            return 100;
        }
    }
    
    $repository->filter(new PrioritizedFilter()); // Added at priority level 100

Conflicting filters
-------------------

If you have two filters that can conflict with another (for instance if they
create a where statement on the same column) you may implement the
ConflictingFilterInterface:

    use Doctrine\ORM\QueryBuilder;
    use Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface;

    class ConflictingFilter implements ConflictingFilterInterface
    {
        public function filter(QueryBuilder $qb)
        {
            // Do stuff
        }

        public function getName()
        {
            return 'conflicting_filter';
        }

        public function getConflictingFilters()
        {
            return array('another_filter');
        }
    }
