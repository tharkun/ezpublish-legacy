<?php
//
// Definition of eZContentObjectVersion class
//
// Created on: <18-Apr-2002 10:05:34 bf>
//
// Copyright (C) 1999-2002 eZ systems as. All rights reserved.
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

/*!
  \class eZContentObjectVersion ezcontentobjectversion.php
  \brief The class eZContentObjectVersion handles different versions of an content object
  \ingourp eZKernel

*/

include_once( "lib/ezdb/classes/ezdb.php" );
include_once( "kernel/classes/ezpersistentobject.php" );
include_once( "kernel/classes/eznodeassignment.php" );
include_once( "kernel/classes/ezcontentobject.php" );

include_once( "kernel/classes/ezcontentobjectattribute.php" );
include_once( "kernel/classes/ezcontentobjecttranslation.php" );
include_once( "kernel/classes/datatypes/ezuser/ezuser.php" );
include_once( "kernel/classes/ezcontentclassattribute.php" );


define( "EZ_VERSION_STATUS_DRAFT", 0 );
define( "EZ_VERSION_STATUS_PUBLISHED", 1 );
define( "EZ_VERSION_STATUS_PENDING", 2 );
define( "EZ_VERSION_STATUS_ARCHIVED", 3 );
define( "EZ_VERSION_STATUS_REJECTED", 4 );
//define( "EZ_VERSION_STATUS_SHOWN", 5 );


class eZContentObjectVersion extends eZPersistentObject
{
    function eZContentObjectVersion( $row=array() )
    {
        $this->ContentObjectAttributeArray = false;
        $this->DataMap = false;
        $this->eZPersistentObject( $row );
    }

    function &definition()
    {
        return array( "fields" => array( 'id' => 'ID',
                                         'contentobject_id' => 'ContentObjectID',
                                         'creator_id' => 'CreatorID',
                                         'version' => 'Version',
                                         'status' => 'Status',
                                         'created' => 'Created',
                                         'modified' => 'Modified',
                                         'workflow_event_pos' => 'WorkflowEventPos'
                                         ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array( // 'data' => 'fetchData',
                                                      'creator' => 'creator',
                                                      'main_parent_node_id' => 'mainParentNodeID',
                                                      "contentobject_attributes" => "contentObjectAttributes",
                                                      "related_contentobject_array" => "relatedContentObjectArray",
                                                      'parent_nodes' => 'parentNodes',
                                                      "can_read" => "canVersionRead",
                                                      "data_map" => "dataMap",
                                                      'node_assignments' => 'nodeAssignments',
                                                      'contentobject' => 'contentObject',
                                                      'language_list' => 'translations',
                                                      'translation' => 'translation',
                                                      'translation_list' => 'translationList'
                                                      ),
                      'class_name' => "eZContentObjectVersion",
                      'sort' => array( 'version' => 'asc' ),
                      'name' => 'ezcontentobject_version' );
    }

    function statusList()
    {
        return array( array( 'name' => "Draft", 'id' =>  EZ_VERSION_STATUS_DRAFT ),
                      array( 'name' => "Published", 'id' =>  EZ_VERSION_STATUS_PUBLISHED ),
                      array( 'name' => "Pending", 'id' =>  EZ_VERSION_STATUS_PENDING ),
                      array( 'name' => "Archived", 'id' =>  EZ_VERSION_STATUS_ARCHIVED ),
                      array( 'name' => "Rejected", 'id' =>  EZ_VERSION_STATUS_REJECTED ) );
    }
    /*!
     \return true if the requested attribute exists in object.
    */

    function hasAttribute( $attr )
    {
        return $attr == 'creator'
            or $attr == 'main_parent_node_id'
            or $attr == 'parent_nodes'
            or $attr == 'node_assignments'
            or $attr == 'contentobject'
            or $attr == 'language_list'
            or $attr == 'translation'
            or $attr == 'translation_list'
            or eZPersistentObject::hasAttribute( $attr );
    }

    function &fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZContentObjectVersion::definition(),
                                                null,
                                                array( 'id' => $id ),
                                                $asObject );
    }



    /*!
     \return the attribute with the requested name.
    */
    function &attribute( $attr )
    {
        if ( $attr == 'creator' )
        {
            return $this->creator();
        }
        elseif ( $attr == 'main_parent_node_id' )
        {
            return $this->mainParentNodeID();
        }
        elseif ( $attr == 'parent_nodes' )
        {
            return  $this->parentNodes();
        }
        elseif ( $attr == 'node_assignments' )
        {
            return  $this->nodeAssignments();
        }
        elseif ( $attr == 'contentobject' )
        {
            return  $this->contentObject();
        }
        else if ( $attr == "data_map" )
        {
            return $this->dataMap();
        }
        elseif ( $attr == 'contentobject_attributes' )
        {
            return  $this->contentObjectAttributes();
        }
        elseif ( $attr == 'related_contentobject_array' )
        {
            return  $this->relatedContentObjectArray();
        }
        elseif ( $attr == 'language_list' )
        {
            return  $this->translations();
        }
        elseif ( $attr == 'translation' )
        {
            return  $this->translation();
        }
        elseif ( $attr == 'translation_list' )
        {
            return  $this->translationList( eZContentObject::defaultLanguage() );
        }
        else if ( $attr == "can_versionread" )
            return $this->canVersionRead();
        else
        {
            return eZPersistentObject::attribute( $attr );
        }
    }

    /*!
     Returns true if the current
    */
    function canVersionRead( )
    {
        if ( !isset( $this->Permissions["can_versionread"] ) )
        {
            $this->Permissions["can_versionread"] = $this->checkAccess( 'versionread' );
        }
        $p = ( $this->Permissions["can_versionread"] == 1 );
//         eZDebug::writeDebug( $p ? "true" : "false", 'p' );
        return $p;
    }

    function checkAccess( $functionName, $classID = false, $parentClassID = false )
    {
        $user =& eZUser::currentUser();
        $userID = $user->attribute( 'contentobject_id' );
        $accessResult =  $user->hasAccessTo( 'content' , $functionName );
        $accessWord = $accessResult['accessWord'];
        $object =& $this->attribute( 'contentobject' );
        $objectClassID = $object->attribute( 'contentclass_id' );
        if ( ! $classID )
        {
            $classID = $objectClassID;
        }
//         eZDebug::writeDebug( $accessWord, 'accessword' );
        if ( $accessWord == 'yes' )
        {
            return 1;
        }
        elseif ( $accessWord == 'no' )
        {
            return 0;
        }
        else
        {
            $policies  =& $accessResult['policies'];
//             eZDebug::writeDebug( $policies, 'policies' );
            foreach ( array_keys( $policies ) as $key  )
            {
                $policy =& $policies[$key];
                $limitationList[] =& $policy->attribute( 'limitations' );
            }
            if ( count( $limitationList ) > 0 )
            {
                $access = 'denied';
                foreach ( array_keys( $limitationList ) as $key  )
                {
                    $limitationArray =& $limitationList[ $key ];
                    if ( $access == 'allowed' )
                    {
                        break;
                    }
                    foreach ( array_keys( $limitationArray ) as $key  )
//                    foreach ( $limitationArray as $limitation )
                    {
                        $limitation =& $limitationArray[$key];
//                        if ( $functionName == 'remove' )
//                        {
//                            eZDebug::writeNotice( $limitation, 'limitation in check access' );
//                        }

                        if ( $limitation->attribute( 'identifier' ) == 'Class' )
                        {
                            if ( $functionName == 'create' )
                            {
                                $access = 'allowed';
                            }
                            elseif ( in_array( $objectClassID, $limitation->attribute( 'values_as_array' )  )  )
                            {
                                $access = 'allowed';
                            }
                            else
                            {
                                $access = 'denied';
                                break;
                            }
                        }
                        elseif ( $limitation->attribute( 'identifier' ) == 'Status' )
                        {

                            if (  in_array( $this->attribute( 'status' ), $limitation->attribute( 'values_as_array' )  ) )
                            {
                                $access = 'allowed';
                            }
                            else
                            {
                                $access = 'denied';
                                break;
                            }
                        }
//                         elseif ( $limitation->attribute( 'identifier' ) == 'ParentClass' )
//                         {

//                             if (  in_array( $this->attribute( 'contentclass_id' ), $limitation->attribute( 'values_as_array' )  ) )
//                             {
//                                 $access = 'allowed';
//                             }
//                             else
//                             {
//                                 $access = 'denied';
//                                 break;
//                             }
//                         }
                        elseif ( $limitation->attribute( 'identifier' ) == 'Section' )
                        {
                            if (  in_array( $object->attribute( 'section_id' ), $limitation->attribute( 'values_as_array' )  ) )
                            {
                                $access = 'allowed';
                            }
                            else
                            {
                                $access = 'denied';
                                break;
                            }
                        }
                        elseif ( $limitation->attribute( 'identifier' ) == 'Owner' )
                        {
                            if ( $this->attribute( 'creator_id' ) == $userID )
                            {
                                $access = 'allowed';
                            }
                            else
                            {
                                $access = 'denied';
                                break;
                            }
                        }

                    }
                }
                if ( $access == 'denied' )
                {
                    return 0;
                }
                else
                {
                    return 1;
                }
            }
        }
    }

    function &contentObject()
    {
        if( !isset( $this->ContentObject ) )
        {
            $this->ContentObject =& eZContentObject::fetch( $this->attribute( 'contentobject_id' ) );
        }
        return $this->ContentObject;
    }

    function mainParentNodeID()
    {
        $temp =& eZNodeAssignment::fetchForObject( $this->attribute( 'contentobject_id' ), $this->attribute( 'version' ), 1 );
        if( $temp == null )
        {
            return 1;
        }
        return $temp[0]->attribute( 'parent_node' );
    }

    function &parentNodes( )
    {
        $retNodes = array();
        $nodeAssignmentList =& eZNodeAssignment::fetchForObject( $this->attribute( 'contentobject_id' ),$this->attribute( 'version' ) );
        foreach ( array_keys( $nodeAssignmentList ) as $key )
        {
            $nodeAssignment =& $nodeAssignmentList[$key];
            /*  if( $nodeAssignment->attribute( 'parent_node' ) != '1' )
            {
                $retNodes[] =& $nodeAssignment;
            }*/
            $retNodes[] =& $nodeAssignment;
        }
        return $retNodes;
    }
    function &nodeAssignments()
    {
        $nodeAssignmentList = array();
        $nodeAssignmentList =& eZNodeAssignment::fetchForObject( $this->attribute( 'contentobject_id' ),$this->attribute( 'version' ) );
//         eZDebug::writeDebug( $nodeAssignmentList, "nodeAssignmentList" );
        return $nodeAssignmentList;
    }

    function &assignToNode( $nodeID, $main = 0, $fromNodeID = 0 )
    {
         $nodeAssignment =&  eZNodeAssignment::create( array( 'contentobject_id' => $this->attribute( 'contentobject_id' ),
                                                              'contentobject_version' => $this->attribute( 'version' ),
                                                              'parent_node' => $nodeID,
                                                              'is_main' => $main,
                                                              'from_node_id' => $fromNodeID
                                                              )
                                                       );
         $nodeAssignment->store();
         return $nodeAssignment;
    }
    function removeAssignment( $nodeID )
    {
        $nodeAssignmentList =& $this->attribute( 'node_assignments' );
        foreach ( array_keys( $nodeAssignmentList ) as $key  )
        {
            $nodeAssignment =& $nodeAssignmentList[$key];
            if ( $nodeAssignment->attribute( 'parent_node' ) == $nodeID )
            {
                $nodeAssignment->remove();
            }
        }
    }

    /*!
     Returns the attributes for the current content object version. The wanted language
     must be specified.
    */
/*    function &contentObjectAtributes( $language = false, $asObject = true )
    {
        return eZContentObject::contentObjectAttributes( $asObject, $this->Version, $language );
        if ( $language === false )
        {
            $language = eZContentObject::defaultLanguage();
        }

        return eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(),
                                                    null, array( "version" => $this->Version,
                                                                 "contentobject_id" => $this->ContentObjectID,
                                                                 "language_code" => $language
                                                                 ),
                                                    null, null,
                                                    $as_object );
    }
*/

    /*!
	 \return the content object attribute
    */
    function &dataMap()
    {
        if ( $this->ContentObjectAttributeArray === false )
        {
            $data =& $this->contentObjectAttributes();
            // Store the attributes for later use
            $this->ContentObjectAttributeArray =& $data;
        }
        else
        {
            $data =& $this->ContentObjectAttributeArray;
        }

        if ( $this->DataMap == false )
        {
            $ret = array();
            reset( $data );
            while( ( $key = key( $data ) ) !== null )
            {
                $item =& $data[$key];

                $identifier = $item->contentClassAttributeIdentifier();

                $ret[$identifier] =& $item;

                next( $data );
            }
            $this->DataMap =& $ret;
        }
        else
        {
            $ret =& $this->DataMap;
        }
        return $ret;
    }

    function resetDataMap()
    {
        $this->ContentObjectAttributeArray = false;
        $this->DataMap = false;
        return $this->DataMap;
    }

    /*!
     Returns the related objects.
    */
    function &relatedContentObjectArray()
    {
        $objectID = $this->attribute( 'contentobject_id' );
        eZDebug::writeDebug( $objectID, "contentobject_id1111111111" );
//        eZDebug::writeDebug( eZContentObject::relatedContentObjectArray( $this->Version ), "related objects" );
        return eZContentObject::relatedContentObjectArray( $this->Version, $objectID );
    }

    function create( $contentobjectID, $userID = false, $version = 1 )
    {
        if ( $userID === false )
        {
            $user =& eZUser::currentUser();
            $userID =& $user->attribute( 'contentobject_id' );
        }
        include_once( 'lib/ezlocale/classes/ezdatetime.php' );
        $row = array(
            "contentobject_id" => $contentobjectID,
            "version" => $version,
            "created" => eZDateTime::currentTimeStamp(),
            "modified" => eZDateTime::currentTimeStamp(),
            'creator_id' => $userID );
        return new eZContentObjectVersion( $row );
    }

    function remove()
    {
        $contentobjectID = $this->attribute( 'contentobject_id' );
        $versionNum = $this->attribute( 'version' );

        $db =& eZDB::instance();
        $db->query( "DELETE FROM ezcontentobject_link
                         WHERE from_contentobject_id=$contentobjectID AND from_contentobject_version=$versionNum" );
        $db->query( "DELETE FROM eznode_assignment
                         WHERE contentobject_id=$contentobjectID AND contentobject_version=$versionNum" );

        $db->query( 'DELETE FROM ezcontentobject_version
                         WHERE id=' . $this->attribute( 'id' ) );

        $contentObjectTranslations =& $this->translations();

        foreach ( array_keys( $contentObjectTranslations ) as $contentObjectTranslationKey )
        {
            $contentObjectTranslation =& $contentObjectTranslations[$contentObjectTranslationKey];
            $contentObjectAttributes =& $contentObjectTranslation->objectAttributes();
            foreach ( array_keys( $contentObjectAttributes ) as $attributeKey )
            {
                $attribute =& $contentObjectAttributes[$attributeKey];
                $attribute->remove( $attribute->attribute( 'id' ), $versionNum );
            }
        }
    }

    function removeTranslation( $languageCode )
    {
//         eZDebug::writeDebug( $this, 'removeTranslation:version' );
        $versionNum = $this->attribute( 'version' );

        $contentObjectAttributes =& $this->contentObjectAttributes( $languageCode );

        foreach ( array_keys( $contentObjectAttributes ) as $attributeKey )
        {
            $attribute =& $contentObjectAttributes[$attributeKey];
            $attribute->remove( $attribute->attribute( 'id' ), $versionNum );
        }
    }

    /*!
     Clones the version with new version \a $newVersionNumber and creator \a $userID
     \note The cloned version is not stored.
    */
    function &clone( $newVersionNumber, $userID )
    {
        $clonedVersion = $this;
        $clonedVersion->setAttribute( 'id', null );
        $clonedVersion->setAttribute( 'version', $newVersionNumber );
        include_once( 'lib/ezlocale/classes/ezdatetime.php' );
        $clonedVersion->setAttribute( 'created', eZDateTime::currentTimeStamp() );
        $clonedVersion->setAttribute( 'modified', eZDateTime::currentTimeStamp() );
        $clonedVersion->setAttribute( 'creator_id', $userID );
        $clonedVersion->setAttribute( 'status', EZ_VERSION_STATUS_DRAFT );
        return $clonedVersion;
    }

    /*!
     Returns an array with all the translations for the current version.
    */
    function translations( $asObject = true )
    {
        return $this->translationList( false, $asObject );
    }

    /*!
     Returns an array with all the translations for the current version.
    */
    function translation( $asObject = true )
    {
        return new eZContentObjectTranslation( $this->ContentObjectID, $this->Version, eZContentObject::defaultLanguage() );
    }

    /*!
     Returns an array with all the translations for the current version.
    */
    function translationList( $language = false, $asObject = true )
    {
        $db =& eZDB::instance();

        $languageSQL = '';
        if ( $language !== false )
        {
            $languageSQL = "AND language_code!='$language'";
        }
        $query = "SELECT language_code FROM ezcontentobject_attribute
                  WHERE contentobject_id='$this->ContentObjectID' AND version='$this->Version'
                        $languageSQL
                  GROUP BY language_code";

        $languageCodes =& $db->arrayQuery( $query );

        $translations = array();
        if ( $asObject )
        {
            foreach ( $languageCodes as $languageCode )
            {
                $translations[] = new eZContentObjectTranslation( $this->ContentObjectID, $this->Version, $languageCode["language_code"] );
            }
        }
        else
        {
            foreach ( $languageCodes as $languageCode )
            {
                $translations[] = $languageCode["language_code"];
            }
        }

        return $translations;
    }


    function &fetchVersion( $version, $contentObjectID, $asObject = true )
    {
        $ret =& eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                     null, array( "version" => $version,
                                                                  "contentobject_id" => $contentObjectID
                                                                 ),
                                                    null, null,
                                                     $asObject );
        return $ret[0];
    }

    function fetchUserDraft( $objectID, $userID )
    {
        $versions =& eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                          null, array( 'creator_id' => $userID,
                                                                       'contentobject_id' => $objectID,
                                                                       'status' => array( array( EZ_VERSION_STATUS_DRAFT, EZ_VERSION_STATUS_DRAFT ) )
                                                                       ),
                                                          array( 'version' => 'asc' ), null,
                                                          true );
        return $versions[0];
    }
    function &fetchForUser( $userID, $status = EZ_VERSION_STATUS_DRAFT )
    {
        $versions =& eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                          null, array( 'creator_id' => $userID,
                                                                       'status' => $status
                                                                       ),
                                                          null, null,
                                                          true );
        return $versions;
    }

    /*!
     Returns the attributes for the current content object version.
     If \a $language is not specified it will use eZContentObject::defaultLanguage.
    */
    function &contentObjectAttributes( $language = false, $asObject = true )
    {
        if ( $language === false )
        {
            $language = eZContentObject::defaultLanguage();
        }

        return eZContentObjectVersion::fetchAttributes( $this->Version, $this->ContentObjectID, $language, $asObject );
    }

    /*!
     Returns the attributes for the content object version \a $version and content object \a $contentObjectID.
     \a $language defines the language to fetch.
     \static
     \sa attributes
    */
    function &fetchAttributes( $version, $contentObjectID, $language, $asObject = true )
    {
        $db =& eZDB::instance();

        $query = "SELECT ezcontentobject_attribute.* from ezcontentobject_attribute, ezcontentclass_attribute
                  WHERE
                    ezcontentclass_attribute.version = '0' AND
                    ezcontentclass_attribute.id = ezcontentobject_attribute.contentclassattribute_id AND
                    ezcontentobject_attribute.version = '$version' AND
                    ezcontentobject_attribute.contentobject_id = '$contentObjectID' AND
                    ezcontentobject_attribute.language_code = '$language'
                  ORDER by
                    ezcontentclass_attribute.placement ASC";

        $attributeArray =& $db->arrayQuery( $query );

        $returnAttributeArray = array();
        foreach ( $attributeArray as $attribute )
        {
            $attr = new eZContentObjectAttribute( $attribute );
            $returnAttributeArray[] = $attr;
        }
        return $returnAttributeArray;
    }

    /*!
     \return the creator of the current version.
    */
    function &creator()
    {
        return eZContentObject::fetch( $this->CreatorID );
    }

    /*!
     \returns an array with locale objects, these objects represents the languages the content objects are allowed to be translated into.
              The array will not include locales that has been translated in this version.
    */
    function &nonTranslationList()
    {
        $translationList =& eZContentObject::translationList();
        if ( $translationList === null )
            return null;
        $translations =& $this->translations( false );
        $nonTranslationList = array();
        foreach ( $translationList as $translationItem )
        {
            $locale = $translationItem->attribute( 'locale_code' );
            if ( !in_array( $locale, $translations ) )
                $nonTranslationList[] = $translationItem;
        }
        return $nonTranslationList;
    }
}

?>
