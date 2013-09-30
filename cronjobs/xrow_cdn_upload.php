<?php
//
// Created on: <01-Sep-09 12:00:00 xrow>
//
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 4.1.3
// BUILD VERSION: 23650
// COPYRIGHT NOTICE: Copyright (C) 1999-2009 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//

/*! \file xrow_cdn_upload.php
*/

$cdn = xrowCDN::getInstance( );
$newtime = new DateTime();

// We don't update the distribution here
# $since = xrowCDN::getLatestUpdateDistribution();
# xrowCDN::updateDistributionFiles( $since );
# xrowCDN::setLatestDistributionUpdate( $newtime );
// We don't update the distribution here

$since = xrowCDN::getLatestUpdateDatabase();
xrowCDN::updateDatabaseFiles( $since );
xrowCDN::setLatestDatabaseUpdate( $newtime );

$cli->output( 'Cronjob xrowCDN finished...');

?>