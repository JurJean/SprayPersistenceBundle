Spray\PersistenceBundle
=======================

A Symfony2 bundle that enhances Doctrine2 repository functionality.


EntityFilters
-------------

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

    class EntityBeforeDate extends AbstractBeforeDateTimeFilter
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

    $repository = new Repository();
    $repository->filter(new EntityPublished());
    $repository->filter(new EntityBeforeDate());
    $repository->filter(new EntityWithinRadius());