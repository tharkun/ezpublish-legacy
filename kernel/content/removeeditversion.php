<?php
//
//
// Created on: <10-Dec-2002 16:02:26 wy>
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

/*! \file removeobject.php
*/
include_once( "kernel/classes/ezcontentobject.php" );
include_once( "lib/ezutils/classes/ezhttppersistence.php" );
include_once( "lib/ezdb/classes/ezdb.php" );
$Module =& $Params["Module"];
$http =& eZHTTPTool::instance();
$objectID = $http->sessionVariable( "DiscardObjectID" );
$version = $http->sessionVariable( "DiscardObjectVersion" );
$editLanguage = $http->sessionVariable( "DiscardObjectLanguage" );

if ( $http->hasPostVariable( "ConfirmButton" ) )
{
    $db =& eZDB::instance();
    $db->query( "DELETE FROM ezcontentobject_link
		         WHERE from_contentobject_id=$objectID AND from_contentobject_version=$version" );
    $db->query( "DELETE FROM eznode_assignment
	             WHERE contentobject_id=$objectID AND contentobject_version=$version" );

    $object =& eZContentObject::fetch( $objectID );
    if ( $object === null )
        return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
    $versionObject =& $object->version( $version );
    $contentObjectAttributes =& $versionObject->contentObjectAttributes( $editLanguage );
    foreach ( $contentObjectAttributes as $contentObjectAttribute )
    {
        $objectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $contentObjectAttribute->remove( $objectAttributeID, $version );
    }
    $versionCount= $object->getVersionCount();
    $nodeID = $object->attribute( 'main_node_id' );
    if ( $versionCount == 1 )
    {
        $object->remove();
    }

    $versionObject->remove();
    if ( $nodeID != null )
        $Module->redirectTo( '/content/view/full/' . $nodeID .'/' );
    else
        $Module->redirectTo( '/content/view/full/2/' );
}

if ( $http->hasPostVariable( "CancelButton" ) )
{
    $Module->redirectTo( '/content/edit/' . $objectID . '/' . $version . '/' );
}
$Module->setTitle( "Remove Editing Version" );
include_once( "kernel/common/template.php" );
$tpl =& templateInit();
$tpl->setVariable( "Module", $Module );
$Result = array();
$Result['content'] =& $tpl->fetch( "design:content/removeeditversion.tpl" );
$Result['path'] = array( array( 'url' => '/content/removeeditversion/',
                                'text' => 'Remove editing version' ) );
?>

