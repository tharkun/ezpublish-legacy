<?php
//
// Definition of eZOperationHandler class
//
// Created on: <06-Oct-2002 16:25:10 amos>
//
// Copyright (C) 1999-2003 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ publish professional licences" may use this
// file in accordance with the "eZ publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" is available at
// http://ez.no/home/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file ezoperationhandler.php
*/

/*!
  \class eZOperationHandler ezoperationhandler.php
  \brief The class eZOperationHandler does

*/

include_once( 'lib/ezutils/classes/ezmoduleoperationinfo.php' );

class eZOperationHandler
{
    /*!
     Constructor
    */
    function eZOperationHandler()
    {
    }

    function &moduleOperationInfo( $moduleName )
    {
        $globalModuleOperationList =& $GLOBALS['eZGlobalModuleOperationList'];
        if ( !isset( $globalModuleOperationList ) )
            $globalModuleOperationList = array();
        if ( isset( $globalModuleOperationList[$moduleName] ) )
            return $globalModuleOperationList[$moduleName];
        $moduleOperationInfo = new eZModuleOperationInfo( $moduleName );
        $moduleOperationInfo->loadDefinition();
        $globalModuleOperationList[$moduleName] =& $moduleOperationInfo;
        return $moduleOperationInfo;
    }

    function &execute( $moduleName, $operationName, $operationParameters, $lastTriggerName = null )
    {
        $moduleOperationInfo =& eZOperationHandler::moduleOperationInfo( $moduleName );
        if ( !$moduleOperationInfo->isValid() )
        {
            eZDebug::writeError( "Cannot execute operation '$operationName' in module '$moduleName', no valid data",
                                  'eZOperationHandler::execute' );
            return null;
        }
        return $moduleOperationInfo->execute( $operationName, $operationParameters, $lastTriggerName );
    }
}

?>
