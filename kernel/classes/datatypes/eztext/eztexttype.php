<?php
//
// Definition of eZTextType class
//
// Created on: <06-May-2002 20:02:55 bf>
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

//!! eZKernel
//! The class eZTextType does
/*!

*/

include_once( "kernel/classes/ezdatatype.php" );

define( "EZ_DATATYPESTRING_TEXT", "eztext" );
define( 'EZ_DATATYPESTRING_TEXT_COLS_FIELD', 'data_int1' );
define( 'EZ_DATATYPESTRING_TEXT_COLS_VARIABLE', '_eztext_cols_' );

class eZTextType extends eZDataType
{
    function eZTextType()
    {
        $this->eZDataType( EZ_DATATYPESTRING_TEXT, "Text field" );
    }

    /*!
     Set class attribute value for template version
    */
    function initializeClassAttribute( &$classAttribute )
    {
        if ( $classAttribute->attribute( EZ_DATATYPESTRING_TEXT_COLS_FIELD ) == null )
            $classAttribute->setAttribute( EZ_DATATYPESTRING_TEXT_COLS_FIELD, 10 );
        $classAttribute->store();
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( &$contentObjectAttribute, $currentVersion )
    {
         $contentClassAttribute =& $contentObjectAttribute->contentClassAttribute();
         if ( $contentClassAttribute->attribute( "data_int1" ) == 0 )
         {
              $contentClassAttribute->setAttribute( "data_int1", 10 );
              $contentClassAttribute->store();
         }
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( &$http, $base, &$contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data =& $http->postVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute =& $contentObjectAttribute->contentClassAttribute();
            if( $classAttribute->attribute( "is_required" ) == true )
            {
                if( $data == "" )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Text field is empty, content required.',
                                                                         'eZStringType' ) );
                    return EZ_INPUT_VALIDATOR_STATE_INVALID;
                }
            }
        }
        return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( &$http, $base, &$contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data =& $http->postVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) );
            $contentObjectAttribute->setAttribute( "data_text", $data );
            return true;
        }
        return false;
    }

    /*!
     Store the content.
    */
    function storeObjectAttribute( &$attribute )
    {
    }

    /*!
     Returns the content.
    */
    function &objectAttributeContent( &$contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    function fetchClassAttributeHTTPInput( &$http, $base, &$classAttribute )
    {
        $column = $base .EZ_DATATYPESTRING_TEXT_COLS_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $column ) )
        {
            $columnValue = $http->postVariable( $column );
            $classAttribute->setAttribute( EZ_DATATYPESTRING_TEXT_COLS_FIELD,  $columnValue );
        }
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    /*!
     Returns the text.
    */
    function title( &$data_instance )
    {
        return $data_instance->attribute( "data_text" );
    }

    /*!
     \reimp
    */
    function isIndexable()
    {
        return true;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return true;
    }

    /*!
     \reuturn the collect information action if enabled
    */
    function contentActionList( &$classAttribute )
    {
        if ( $classAttribute->attribute( 'is_information_collector' ) == true )
        {
            return array( array( 'name' => 'Send',
                                 'action' => 'ActionCollectInformation'
                                 ) );
        }
        else
            return array();
    }

    /*!
     \return a DOM representation of the content object attribute
    */
    function &serializeContentObjectAttribute( $objectAttribute )
    {
        include_once( 'lib/ezxml/classes/ezdomdocument.php' );
        include_once( 'lib/ezxml/classes/ezdomnode.php' );

//         $node = new eZDOMNode();
//         $node->setName( 'attribute' );
//         $node->appendAttribute( eZDOMDocument::createAttributeNode( 'name', $objectAttribute->contentClassAttributeName() ) );
//         $node->appendAttribute( eZDOMDocument::createAttributeNode( 'type', 'eztext' ) );
        $node =& eZDataType::contentObjectAttributeDOMNode( $objectAttribute );

        $node->appendChild( eZDOMDocument::createTextNode( $objectAttribute->attribute( 'data_text' ) ) );

        return $node;
    }

}

eZDataType::register( EZ_DATATYPESTRING_TEXT, "eztexttype" );

?>
