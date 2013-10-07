## 10/07/2013 ~ Migrate to the SonataClassificationBundle

The SonataNewsBundle now used the SonataClassificationBundle, so here the main changes:

* Tag namespace changes from ``Sonata\NewsBundle\Model\TagInteface`` => ``Sonata\ClassificationBundle\Model\TagInteface``
* Category is now a Collection object:  ``Sonata\NewsBundle\Model\CategoryInteface`` => ``Sonata\ClassificationBundle\Model\CollectionInteface``
* Update Tag and Category classes from ``Application\NewsBundle`` => ``Application\ClassificationBundle` - Fix namespace reference (Category => Collection)
* Update Tag and Collection doctrine xml from ``Application\NewsBundle`` => ``Application\ClassificationBundle`` - Fix namespace reference (Category => Collection)
* You cannot rely on Tag.posts or Category.posts associations, these associations are gone.

### ClassificationBundle install step

* add "sonata-project/classification-bundle": "~2.2@dev" into your composer.json
* run ``composer update`` to add the code to your project
* add ``new Sonata\ClassificationBundle\SonataClassificationBundle(),`` into ``AppKernel.php``
* generates easy extends model : ``app/console sonata:easy-extends:generate SonataClassificationBundle -d src``
* enable the new Bundle into ``AppKernel.php``: ``new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),``
* run ``app/console doctrine:schema:update --dump-sql``, you should see queries related to the classification bundle. If not please check the ``auto_mapping`` feature from doctrine.
* create a migration file ``app/console doctrine:migrations:diff``, this should generate a new file : something like ``/vagrant/sonata-site/app/DoctrineMigrations/Version20131007062821.php``
* run the migration ``app/console doctrine:migration:migrate``

### Migrate NewsBundle code

* remove Category and Tag models : ``rm src/Application/Sonata/NewsBundle/Entity/(Category|Tag).php``
* remove Category and Tag configurations : ``rm src/Application/Sonata/NewsBundle/Resources/config/doctrine/(Category|Tag)*.xml``
* If you have tweaked model classes through the configuration, please adjust the change ...

### Migrate database data

* Create a migration file ``app/console doctrine:migrations:diff``, this should generate a new file : something like ``/vagrant/sonata-site/app/DoctrineMigrations/Version20131007064324.php``
* DOES NOT RUN THE MIGRATION!
* Edit the migration file to match the provided one:
    * This line ``ALTER TABLE news__post_tag DROP FOREIGN KEY FK_682B2051BAD`` is set twice, remove the last one
    * Add new SQL lines to migrate data
  
The complete migration file should look like:

```php
<?php

class Version20131007064324 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("SET foreign_key_checks = 0;");
        $this->addSql("INSERT INTO classification__tag (id, name, enabled, slug, created_at, updated_at) SELECT * from news__tag");
        $this->addSql("INSERT INTO classification__tag_audit (id, rev, name, enabled, slug, created_at, updated_at, revtype) SELECT * from news__tag_audit");
        $this->addSql("INSERT INTO classification__collection (id, name, enabled, slug, description, created_at, updated_at) SELECT id, name, enabled, slug, description, created_at, updated_at FROM news__category;");
        $this->addSql("INSERT INTO classification__collection_audit (id, rev, name, enabled, slug, description, created_at, updated_at, revtype) SELECT id, rev, name, enabled, slug, description, created_at, updated_at, revtype FROM news__category_audit;");
        $this->addSql("ALTER TABLE news__post DROP FOREIGN KEY FK_7D109BC812469DE2");
        $this->addSql("ALTER TABLE news__post_tag DROP FOREIGN KEY FK_682B2051BAD26311");
        $this->addSql("DROP TABLE news__category");
        $this->addSql("DROP TABLE news__category_audit");
        $this->addSql("DROP TABLE news__tag");
        $this->addSql("DROP TABLE news__tag_audit");
        $this->addSql("DROP INDEX IDX_7D109BC812469DE2 ON news__post");
        $this->addSql("ALTER TABLE news__post CHANGE category_id collection_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE news__post ADD CONSTRAINT FK_7D109BC8514956FD FOREIGN KEY (collection_id) REFERENCES classification__collection (id)");
        $this->addSql("CREATE INDEX IDX_7D109BC8514956FD ON news__post (collection_id)");
        $this->addSql("ALTER TABLE news__post_tag ADD CONSTRAINT FK_682B2051BAD26311 FOREIGN KEY (tag_id) REFERENCES classification__tag (id)");
        $this->addSql("ALTER TABLE news__post_audit CHANGE category_id collection_id INT DEFAULT NULL");
        $this->addSql("SET foreign_key_checks = 1;");
    }

    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException();
    }
}

```
