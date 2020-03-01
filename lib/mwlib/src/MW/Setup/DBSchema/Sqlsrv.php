<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\DBSchema;


/**
 * Implements querying the SQL server database
 *
 * @package MW
 * @subpackage Setup
 */
class Sqlsrv extends \Aimeos\MW\Setup\DBSchema\InformationSchema
{
	/**
	 * Checks if the given table exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @return bool True if the table exists, false if not
	 */
	public function tableExists( string $tablename ) : bool
	{
		$sql = "
			SELECT name
			FROM sys.tables
			WHERE SCHEMA_NAME(schema_id) = ?
				AND name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Checks if the given column exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return bool True if the column exists, false if not
	 */
	public function columnExists( string $tablename, string $columnname ) : bool
	{
		$sql = "
			SELECT name
			FROM sys.columns c
			WHERE SCHEMA_NAME(schema_id) = ?
				AND OBJECT_NAME(object_id) = ?
				AND c.name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $columnname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Checks if the given constraint exists for the specified table in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $constraintname Name of the database table constraint
	 * @return bool True if the constraint exists, false if not
	 */
	public function constraintExists( string $tablename, string $constraintname ) : bool
	{
		$sql = "
			SELECT name
			FROM sys.foreign_keys
			WHERE SCHEMA_NAME(schema_id) = ?
				AND OBJECT_NAME(parent_object_id) = ?
				AND name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $constraintname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Checks if the given sequence exists in the database.
	 *
	 * @param string $seqname Name of the database sequence
	 * @return bool True if the sequence exists, false if not
	 */
	public function sequenceExists( string $seqname ) : bool
	{
		return false;
	}


	/**
	 * Checks if the given index (not foreign keys, primary or unique constraints) exists in the database.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $indexname Name of the database index
	 * @return bool True if the index exists, false if not
	 */
	public function indexExists( string $tablename, string $indexname ) : bool
	{
		$sql = "
			SELECT name
			FROM sys.indexes
			WHERE SCHEMA_NAME(schema_id) = ?
				AND OBJECT_NAME(object_id) = ?
				AND name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $indexname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		return $result ? true : false;
	}


	/**
	 * Returns an object containing the details of the column.
	 *
	 * @param string $tablename Name of the database table
	 * @param string $columnname Name of the table column
	 * @return \Aimeos\MW\Setup\DBSchema\Column\Iface Object which contains the details
	 */
	public function getColumnDetails( string $tablename, string $columnname ) : \Aimeos\MW\Setup\DBSchema\Column\Iface
	{
		$sql = "
			SELECT t.name AS table_name, c.name AS col_name, p.name AS type_name,
				OBJECT_DEFINITION(c.default_object_id) AS col_default,
				c.max_length, c.precision
			FROM sys.columns c
			JOIN sys.tables t ON c.object_id = t.object_id
			JOIN sys.types p ON p.user_type_id = c.user_type_id
			WHERE SCHEMA_NAME(schema_id) = ?
				AND t.name = ?
				AND c.name = ?
		";

		$conn = $this->acquire();

		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $this->getDBName() );
		$stmt->bind( 2, $tablename );
		$stmt->bind( 3, $columnname );
		$result = $stmt->execute()->fetch();

		$this->release( $conn );

		if( $result === null ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unknown column "%1$s" in table "%2$s"', $columnname, $tablename ) );
		}

		$length = ( isset( $record['precision'] ) ? $record['precision'] : $record['max_length'] );

		return new \Aimeos\MW\Setup\DBSchema\Column\Item( $record['table_name'], $record['col_name'], $record['type_name'],
			$length, $record['col_default'], $record['is_nullable'], null, $record['collation_name'] );
	}
}
