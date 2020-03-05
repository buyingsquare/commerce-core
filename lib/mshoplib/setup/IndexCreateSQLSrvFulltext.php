<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates the full text index on mshop_index_text.content for SQL Server
 */
class IndexCreateSQLSrvFulltext extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Creating full text index on "mshop_index_text.content" for SQL Server', 0 );

		$schema = $this->getSchema( 'db-product' );
echo 'name: ' . $schema->getName() . PHP_EOL;
echo 'exists: ' . ((int) $schema->tableExists( 'mshop_index_text' )) . PHP_EOL;

$conn = $this->acquire( 'db-product' );
$result = $conn->create( 'SELECT * FROM sys.indexes' )->execute();
while( $row = $result->fetch() ) {
	print_r( $row );
}
$this->release( $conn, 'db-product' );

		if( $schema->getName() === 'sqlsrv' /*&& $schema->tableExists( 'mshop_index_text' )*/ )
		{
			try
			{
				$sql = sprintf( '
					SELECT object_id FROM sys.fulltext_indexes
					WHERE object_id = OBJECT_ID(\'%1$s.mshop_index_text\')
				', $schema->getDBName() );
echo $sql . PHP_EOL;

				echo $this->getValue( $sql, 'object_id', 'db-product' );
				return $this->status( 'OK' );
			}
			catch( \Aimeos\MW\Setup\Exception $e )
			{
				$this->execute( 'CREATE FULLTEXT CATALOG ft AS DEFAULT', 'db-product' );
				$this->execute( 'CREATE FULLTEXT INDEX ON "mshop_index_text" ("content") KEY INDEX PK__mshop_in__3213E83F35AFB77C', 'db-product' );

				return $this->status( 'done' );
			}
		}

		$this->status( 'OK' );
	}
}
