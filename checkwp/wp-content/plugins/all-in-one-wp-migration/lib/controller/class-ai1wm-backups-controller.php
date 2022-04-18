<?php
/**
 * Copyright (C) 2014-2019 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

class Ai1wm_Backups_Controller {

	public static function index() {
		$model = new Ai1wm_Backups;

		Ai1wm_Template::render(
			'backups/index',
			array(
				'backups'        => $model->get_files(),
				'backups_labels' => get_option( AI1WM_BACKUPS_LABELS, array() ),
				'username'       => get_option( AI1WM_AUTH_USER ),
				'password'       => get_option( AI1WM_AUTH_PASSWORD ),
			)
		);
	}

	public static function delete( $params = array() ) {
		$errors = array();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		// Set archive
		$archive = null;
		if ( isset( $params['archive'] ) ) {
			$archive = trim( $params['archive'] );
		}

		try {
			// Ensure that unauthorized people cannot access delete action
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		$model = new Ai1wm_Backups;

		try {
			// Delete file
			$model->delete_file( $archive );

			$backups_labels = get_option( AI1WM_BACKUPS_LABELS, array() );

			if ( isset( $backups_labels[ $params['archive'] ] ) ) {
				unset( $backups_labels[ $params['archive'] ] );
				update_option( AI1WM_BACKUPS_LABELS, $backups_labels );
			}
		} catch ( Exception $e ) {
			$errors[] = $e->getMessage();
		}

		echo json_encode( array( 'errors' => $errors ) );
		exit;
	}

	public static function add_label( $params = array() ) {
		ai1wm_setup_environment();

		$backups_labels = get_option( AI1WM_BACKUPS_LABELS, array() );

		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		if ( empty( $params['backup_label'] ) ) {
			unset( $backups_labels[ trim( $params['backup_name'] ) ] );

			echo json_encode(
				array(
					'success' => update_option( AI1WM_BACKUPS_LABELS, $backups_labels ),
					'label'   => $params['backup_label'],
				)
			);
			exit;
		}

		echo json_encode(
			array(
				'success' => update_option(
					AI1WM_BACKUPS_LABELS,
					array_merge( $backups_labels, array( trim( $params['backup_name'] ) => trim( $params['backup_label'] ) ) )
				),
				'label'   => trim( $params['backup_label'] ),
			)
		);
		exit;
	}
}
