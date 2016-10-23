<?php

/*------------------------------------------------------------------------
# com_affiliatetracker - Affiliate Tracker for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaThat.com
# Technical Support:	Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

function AffiliateTrackerBuildRoute( &$query ) {
	$segments									=	array();
	
	$database				=	JFactory::getDBO();
		if(!isset($query['view'])) $query['view'] = "";
		$view									=	strtolower( $query['view'] );

		switch ( $view ) {
			
			
			case 'conversions':
				
				$segments[] = "conversions" ;
				unset( $query['view'] );
			break;
			case 'logs':
				
				$segments[] = "logs" ;
				unset( $query['view'] );
			break;
			case 'accounts':
				
				$segments[] = "accounts" ;
				unset( $query['view'] );
			break;
			case 'account':
				
				$segments[] = "account" ;
				unset( $query['view'] );
				if($query['id']){
					$segments[] = "edit" ;
					$segments[] = $query['id'] ;
				}
				else{
					$segments[] = "new" ;
				}
				unset( $query['id'] );
				unset( $query['layout'] );
			break;
			case 'payments':
				
				$segments[] = "payments" ;
				unset( $query['view'] );
			break;
			case 'marketings':

				$segments[] = "marketings" ;
				unset( $query['view'] );
				break;
			
			default:
				$segments[] = "conversions" ;
				unset( $query['view'] );
				break;
		}
		

	return $segments;
}

function AffiliateTrackerParseRoute( $segments ) {
	$vars										=	array();

	$database				=	JFactory::getDBO();
		
	$count										=	count( $segments );
	if ( $count > 0 ) {
		
		switch ( $count ) {
			
			case 0: 
					
					$vars['view']				=	$segments[0];
					
				
				break;

			case 1: 
					
					$vars['view']				=	$segments[0];
					
				
				break;
				
			case 2: 
					$vars['view']				=	$segments[0];

					if($segments[1] == "new"){
						$vars['id'] = 0 ;
						$vars['layout'] = "form" ;
					}
				
				break;

			case 3: 
					$vars['view']				=	$segments[0];

					if($segments[1] == "edit"){
						$vars['id'] = $segments[2] ;
						$vars['layout'] = "form" ;
					}
				
				break;

			default:
				break;
		}
	}
	return $vars;
}


?>