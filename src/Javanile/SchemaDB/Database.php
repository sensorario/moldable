<?php
/*
 *
 * Copyright (c) 2012-2015 Bianco Francesco
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files "schemadb", to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
\*/

/*
 *
 * Thanks to SourceForge.net
 * for your mission on the web
 *
\*/
namespace Javanile\SchemaDB;

/**
 * Main class prototyping a SchemaDB connection with MySQL database
 *
 * <code>
 * <?php
 * // Create SchemaDB connection
 * $conn = new SchemaDB(array(
 *		'host' => 'localhost',
 *		'user' => 'root',
 *		'pass' => 'root',
 *		'name' => 'db_schemadb',
 *		'pref' => 'tbl_',
 * ));
 *
 * // Create Table on database
 * $conn->update(array(
 *		'Table1' => array(
 *			'Field1' => 0,
 *			'Field2' => "",
 *		)
 * ));
 * ?>
 * </code>
 */
class Database extends DatabaseRecord
{
    /**
     * Currenti release version number
     */
    const VERSION = '0.3.0';

	/**
	 * Timestamp for benchmark
	 */
	protected $_ts = null;
		
    /**
     *
     *
     * @var type
     */
    protected static $_default = null;

    /**
     * Construct and connect a SchemaDB drive
     * to mysql database best way to start use it
     *
     * @param array $args Array with connection parameters
     */
    public function __construct($args)
    {
		//
		$this->_ts = microtime();	

		// 
		parent::__construct($args);

        //
        static::setDefault($this);
    }

    
    /**
     * Retrieve default SchemaDB connection
     *
     * @return type
     */
    public static function getDefault()
    {
        // return static $default
        return static::$_default;
    }

    /**
     * Set global context default database 
	 * for future use into model management
	 * 
     * @param type $database
     */
    public static function setDefault($database)
    {
        // if no default SchemaDB connection auto-set then-self
        if (static::$_default === null) {

            // set current SchemaDB connection to default
            static::$_default = &$database;
        }
    }
	
	/**
	 * 
	 * 
	 * @param type $confirm
	 * @return type
	 */
	public function drop($confirm) {
		
		if ($confirm != 'confirm') {
			return;
		}
		
		//
		$tables = $this->getTables();
		
		//
		if (!$tables) {
			return;		
		}
		
		//
		foreach($tables as $table) {
			
			//
			$sql = "DROP TABLE `{$table}`";
			
			//
			$this->execute($sql);
		}		
	}
		
	/**
	 * 
	 */
	public function benchmark() {
		
		// 
		echo '<pre style="background:#333;color:#fff;padding:2px 6px 3px 6px;border:1px solid #000">Time: '.(microtime()-$this->_ts).' Mem: '.memory_get_usage(true).'</pre>';
	}

    /**
     *
     *
     */
    public function dump($model=null) {

        //
        if ($model) {
          
            //
            $all = $this->all($model);

            //
            Debug::grid_dump($model,$all);
        }

        //
        else {
            $this->dumpSchema();
        }

    }

}


