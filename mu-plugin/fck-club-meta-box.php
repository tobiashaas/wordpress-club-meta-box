<?php
/**
 * Plugin Name: FCK Club Meta Box
 * Description: Registriert das komplette Club-Schema (Feldgruppen + Settings-Page) per Code aus gebuendelten JSON-Exporten (Meta Box rwmb_meta_boxes / mb_settings_pages). Source of Truth: github.com/tobiashaas/wordpress-club-meta-box. Ersetzt die frueheren MBB-DB-Gruppen + Einzel-mu-plugins.
 * Version: 1.0.0
 * Author: WebAudits / Claude
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Liga-Taxonomie (im WP-Admin erweiterbar) fuer Mannschaften. */
add_action( 'init', function () {
	if ( taxonomy_exists( 'liga' ) ) { return; }
	register_taxonomy( 'liga', [ 'mannschaften' ], [
		'labels'            => [
			'name'          => 'Ligen',
			'singular_name' => 'Liga',
			'all_items'     => 'Alle Ligen',
			'add_new_item'  => 'Neue Liga hinzufügen',
			'new_item_name' => 'Name der neuen Liga',
			'search_items'  => 'Ligen suchen',
			'menu_name'     => 'Ligen',
		],
		'public'            => true,
		'hierarchical'      => false,
		'show_admin_column' => true,
		'show_in_rest'      => true,
	] );
}, 9 );

/** Basisverzeichnis mit MB_FieldGroups/ + MB_Settings_Page/ finden (Server-Bundle ODER Repo-Layout). */
function fck_cmb_base_dir() {
	$candidates = [
		__DIR__ . '/fck-club-meta-box', // Server: mu-plugins/fck-club-meta-box/
		dirname( __DIR__ ),             // Repo: mu-plugin/.. -> Repo-Root
		__DIR__,
	];
	foreach ( $candidates as $c ) {
		if ( is_dir( $c . '/MB_FieldGroups' ) ) {
			return $c;
		}
	}
	return __DIR__;
}

/** REST fuer alle Felder rekursiv aktivieren (hide_from_rest=false). */
function fck_cmb_force_rest( array &$fields ) {
	foreach ( $fields as &$f ) {
		$f['hide_from_rest'] = false;
		if ( isset( $f['fields'] ) && is_array( $f['fields'] ) ) {
			fck_cmb_force_rest( $f['fields'] );
		}
	}
	unset( $f );
}

/** 1) Settings-Page(s) registrieren. */
add_filter( 'mb_settings_pages', function ( $pages ) {
	$dir = fck_cmb_base_dir() . '/MB_Settings_Page';
	if ( ! is_dir( $dir ) ) { return $pages; }
	foreach ( glob( $dir . '/*.json' ) as $file ) {
		$p = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $p ) || empty( $p['id'] ) ) { continue; }
		unset( $p['$schema'], $p['modified'], $p['customizer'], $p['customizer_only'], $p['network'] );
		$pages[] = $p;
	}
	return $pages;
} );

/** 2) Alle Feldgruppen registrieren. */
add_filter( 'rwmb_meta_boxes', function ( $meta_boxes ) {
	$dir = fck_cmb_base_dir() . '/MB_FieldGroups';
	if ( ! is_dir( $dir ) ) { return $meta_boxes; }
	foreach ( glob( $dir . '/*.json' ) as $file ) {
		$g = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $g ) || empty( $g['fields'] ) ) { continue; }
		unset( $g['$schema'], $g['modified'], $g['autosave'], $g['default_hidden'], $g['exclude'] );
		fck_cmb_force_rest( $g['fields'] );
		$meta_boxes[] = $g;
	}
	return $meta_boxes;
}, 20 );
