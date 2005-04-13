<?php
//
// Definition of Removeorder class
//
// Created on: <03-Mar-2004 09:45:38 wy>
//
// Copyright (C) 1999-2005 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" version 2 is available at
// http://ez.no/ez_publish/licences/professional/ and in the file
// PROFESSIONAL_LICENCE included in the packaging of this file.
// For pricing of this licence please contact us via e-mail to licence@ez.no.
// Further contact information is available at http://ez.no/company/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file removeorder.php
*/

include_once( "kernel/common/template.php" );
include_once( "kernel/classes/ezorder.php" );

$Module =& $Params["Module"];
$http =& eZHTTPTool::instance();
$deleteIDArray = $http->sessionVariable( "DeleteOrderIDArray" );

if ( $http->hasPostVariable( "ConfirmButton" ) )
{
    $db =& eZDB::instance();
    $db->begin();
    foreach ( $deleteIDArray as $deleteID )
    {
        eZOrder::cleanupOrder( $deleteID );
    }
    $db->commit();
    $Module->redirectTo( '/shop/orderlist/' );
}
elseif ( $http->hasPostVariable( "CancelButton" ) )
{
    $Module->redirectTo( '/shop/orderlist/' );
}
else // no action yet: just displaying the template
{
    $orderNumbersArray = array();
    foreach ( $deleteIDArray as $orderID )
    {
        $order = eZOrder::fetch( $orderID );
        if ( is_null( $order ) )
            continue;   // just to prevent possible fatal error below

        $orderNumbersArray[] = $order->attribute( 'order_nr' );
    }
    $orderNumbersString = implode( ', ', $orderNumbersArray );

    $Module->setTitle( ezi18n( 'shop', 'Remove orders' ) );

    $tpl =& templateInit();
    $tpl->setVariable( "module", $Module );
    $tpl->setVariable( "delete_result", $orderNumbersString );
    $Result = array();

    $path = array();
    $path[] = array( 'text' => ezi18n( 'kernel/shop', 'Remove order' ),
                     'url' => false );
    $Result['path'] =& $path;
    $Result['content'] =& $tpl->fetch( "design:shop/removeorder.tpl" );
}
?>
