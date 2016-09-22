<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class EasyBlogFormHelper
{
	public $form 	= null;
	
	public function __construct( $name = '' , $contents = '' , $manifestFile , $xpath = false)
	{
		$version 			= EasyBlogHelper::getJoomlaVersion();

		if( $version >= '3.0' )
		{
			$this->form 	= new JForm( $name );

			if( $xpath == 'params' )
			{
				$xpath	= 'config/fields';
			}

			$this->form->loadFile( $manifestFile , true , $xpath );


			$themeConfig	= EasyBlogHelper::getTable( 'Configs' );
			$themeConfig->load( $name );

			$themeParam 	= EasyBlogHelper::getRegistry( $themeConfig->params );

			$registry 		= EasyBlogHelper::getRegistry( $contents );

			$this->form->bind( $registry->toArray() );
		}
		else
		{
			$this->form		= new JParameter( $contents , $manifestFile );

			$themeConfig	= EasyBlogHelper::getTable( 'Configs' );
			$themeConfig->load( $name );

			// @rule: Overwrite with the settings from the database.
			if( !empty( $themeConfig->params ) )
			{
				$this->form->bind( $themeConfig->params );
			}
		}
	}

	public function getFormValues()
	{
		$result 	= array();

		if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
		{

			foreach( $this->form->getFieldsets() as $name => $fieldset )
			{
				foreach( $this->form->getFieldset( $name ) as $field )
				{
					$obj 	= new stdClass();

					$obj->type 	= $field->type;
					$obj->label	= $field->label;
					$obj->key 	= $field->fieldname;
					$obj->value = $field->value;
					$obj->desc 	= $field->description;
					$obj->input = $field->input;

					$result[]	= $obj;
				}

			}
		}
		else
		{
			$params	= $this->form->getParams( 'params' , '_default' );
			if( count($params) > 0 )
			{
				foreach( $params as $param)
				{
					$obj 	= new stdClass();

					$type = $param[1];

					if( EasyBlogHelper::getJoomlaVersion() <= '2.5' )
					{
						if( strpos($param[1], '</select>') !== false )
						{
							$type   = 'list';
						}
						else
						{
							$type = 'string';
						}
					}

					$obj->type 	= $type;
					$obj->label	= $param[3];
					$obj->key 	= $param[5];
					$obj->value = $param[4];
					$obj->desc 	= $param[2];
					$obj->input = $param[1];

					$result[]	= $obj;
				}

			}
		}

		return $result;
	}

}