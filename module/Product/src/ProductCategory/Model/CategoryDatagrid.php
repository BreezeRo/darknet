<?php 

/**
 * Copyright (c) 2014 Shine Software.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in
 * the documentation and/or other materials provided with the
 * distribution.
 *
 * * Neither the names of the copyright holders nor the names of the
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Product
 * @subpackage Model
 * @author Michelangelo Turillo <mturillo@shinesoftware.com>
 * @copyright 2014 Michelangelo Turillo.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://shinesoftware.com
 * @version @@PACKAGE_VERSION@@
 */

namespace ProductCategory\Model;

use ZfcDatagrid;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Formatter;
use ZfcDatagrid\Filter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CategoryDatagrid {
	
	/**
	 *
	 * @var \ZfcDatagrid\Datagrid
	 */
	protected $grid;
	
	/**
	 *
	 * @var \Zend\Db\Adapter\Adapter
	 */
	protected $adapter;
	
	/**
	 *
	 * @var \Zend\Db\TableGateway\TableGatewaye
	 */
	protected $tableGateway;
	
	/**
	 *
	 * @var SettingsService
	 */
	protected $settings;
	
	/**
	 * Datagrid Constructor
	 * 
	 * @param \Zend\Db\Adapter\Adapter $dbAdapter
	 * @param \ZfcDatagrid\Datagrid $datagrid
	 * @param \Base\Service\SettingsServiceInterface $settings
	 * @param \ProductCategory\Service\CategoryAttributeService $attributes
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $dbAdapter, 
	                            \ZfcDatagrid\Datagrid $datagrid, 
	                            \Base\Service\SettingsServiceInterface $settings,
	                            TableGateway $categoryService
	        )
	{
		$this->adapter = $dbAdapter;
		$this->grid = $datagrid;
		$this->settings = $settings;
		$this->tableGateway = $categoryService;
	}
	
	/**
	 *
	 * @return \ZfcDatagrid\Datagrid
	 */
	public function getGrid()
	{
		return $this->grid;
	}
	
	/**
	 * Category list
	 *
	 * @return \ZfcDatagrid\Datagrid
	 */
	public function getDatagrid()
	{
	    $records = array();
	   
		$grid = $this->getGrid();
		$grid->setId('categoryGrid');
		
		$dbAdapter = $this->adapter;
		$select = new Select();
		$select->from(array ('c' => 'product_category'));
		
		$RecordsPerPage = $this->settings->getValueByParameter('product_category', 'recordsperpage');
			
		$grid->setDefaultItemsPerPage($RecordsPerPage);
		$grid->setDataSource($select, $dbAdapter);
		
		$colId = new Column\Select('id', 'c');
		$colId->setLabel('Id');
		$colId->setIdentity();
		$grid->addColumn($colId);


        $col = new Column\Select('name', 'c');
        $col->setLabel(_('Name'));
        $grid->addColumn($col);

        $col = new Column\Select('position', 'c');
        $col->setLabel(_('Position'));
        $grid->addColumn($col);

        $col = new Column\Select('enabled', 'c');
        $col->setLabel(_('Enabled'));
        $grid->addColumn($col);


        $colType = new Type\DateTime('Y-m-d H:i:s', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
		$colType->setSourceTimezone('Europe/Rome');
		$colType->setOutputTimezone('UTC');
		$colType->setLocale('it_IT');
		
		
		$col = new Column\Select('createdat', 'c');
		$col->setType($colType);
		$col->setLabel(_('Created At'));
		$col->setWidth(15);
		$grid->addColumn($col);
		
		// Add actions to the grid
		$showaction = new Column\Action\Button();
		$showaction->setAttribute('href', "/admin/category/edit/" . $showaction->getColumnValuePlaceholder(new Column\Select('id','c')));
		$showaction->setAttribute('class', 'btn btn-xs btn-success');
		$showaction->setLabel(_('edit'));
		
		$delaction = new Column\Action\Button();
		$delaction->setAttribute('href', '/admin/category/delete/' . $delaction->getRowIdPlaceholder());
		$delaction->setAttribute('onclick', "return confirm('Are you sure?')");
		$delaction->setAttribute('class', 'btn btn-xs btn-danger');
		$delaction->setLabel(_('delete'));
		
		$col = new Column\Action();
		$col->addAction($showaction);
		$col->addAction($delaction);
		$grid->addColumn($col);
		
		$grid->setToolbarTemplate('');
		
		return $grid;
	}

}

?>