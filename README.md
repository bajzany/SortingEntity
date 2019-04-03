## SortingEntity

#### Instalation

- Composer installation

		composer require bajzany/sorting-entity dev-master
	
	
- Registration into extension .neon

		extensions:
			sortingEntity: Bajzany\SortingEntity\DI\SortingEntityExtension	
    	
- For integration into entity use

		repositoryClass="Bundles\Page\Repository\PageRepository"         
		implements ISortingEntity
		use Sortable;
		
		
		
Example:
		
	   /**
         * @ORM\Table(name="page_pages")
         * @ORM\Entity(
         *     repositoryClass="Bundles\Page\Repository\PageRepository"
         * )
        */
        class Page implements ISortingEntity
        {
        
        	use Identifier;
        	use Sortable;
        	
        	
        	......
        	another properity
        }


Repository important functions:

- getSorted($parent = NULL, bool $compareParent = FALSE, $getQueryBuilder = FALSE)
- moveUp(ISortingEntity $entity, ISortingEntity $target)
- moveDown(ISortingEntity $entity, ISortingEntity $target)


##### getSorted

- getSorted entities. Options:
 
 		parent = you can set parentId where you can sorted
 		compareParent = for enabling parent sorting (because parent can be null)
 		getQueryBuilder = now return updated queryBuilder, this option has been good for another filtering data or for another Components whitch want QueryBuilder
 		
##### moveUp

- Move entity over target entity 

##### moveDown

- Move entity under target entity 
