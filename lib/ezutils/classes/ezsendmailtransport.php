<?php
//
// Definition of eZSendmailTransport class
//
// Created on: <10-Dec-2002 14:41:22 amos>
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

/*! \file ezsendmailtransport.php
*/

/*!
  \class eZSendmailTransport ezsendmailtransport.php
  \brief The class eZSendmailTransport does

*/

include_once( 'lib/ezutils/classes/ezmailtransport.php' );

class eZSendmailTransport extends eZMailTransport
{
    /*!
     Constructor
    */
    function eZSendmailTransport()
    {
    }

    /*!
     \reimp
    */
    function sendMail( &$mail )
    {
//         ini_set( 'SMTP', 'mail.ez.no' );
//         ini_set( 'smtp_port', 25 );
        $message = $mail->body();
        $extraHeaders = $mail->headerText();
        return mail( $mail->receiverEmailText(), $mail->subject(), $message, $extraHeaders );
    }
}

?>
