<?php
/**
 * Plugin Name: FCK Club Meta Box
 * Description: Registriert das komplette Club-Schema (Feldgruppen + Settings-Page) per Code aus gebuendelten JSON-Exporten (Meta Box rwmb_meta_boxes / mb_settings_pages). Source of Truth: github.com/tobiashaas/wordpress-club-meta-box. Ersetzt die frueheren MBB-DB-Gruppen + Einzel-mu-plugins.
 * Version: 1.0.0
 * Author: WebAudits / Claude
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Zentrale Liga-Liste. Beide Liga-Felder (Mannschaft 'liga' + Spielplan 'league')
 * ziehen diese Liste automatisch (Wert == Label -> speichert lesbaren Text,
 * Views bleiben unveraendert). Neue Liga? Einfach hier eine Zeile ergaenzen.
 */
function fck_cmb_ligen() {
	return [
		'Verbandsliga',
		'Landesliga',
		'Bezirksliga',
		'Kreisliga A',
		'Kreisliga B',
		'Kreisliga C',
		'Kreisklasse A',
		'Kreisklasse B',
		'Kreisklasse C',
		// Bestand aus dem Spielplan (bei Bedarf bereinigen):
		'Kreisliga B1',
		'Freundschaftsspiel',
	];
}

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

/** REST aktivieren + Liga-Felder ('liga'/'league') zu select_advanced mit zentraler Liste machen (rekursiv). */
function fck_cmb_process_fields( array &$fields ) {
	$ligen = fck_cmb_ligen();
	$opts  = array_combine( $ligen, $ligen );
	foreach ( $fields as &$f ) {
		$f['hide_from_rest'] = false;
		if ( isset( $f['id'] ) && in_array( $f['id'], [ 'liga', 'league' ], true ) ) {
			$f['type']        = 'select_advanced';
			$f['options']     = $opts;
			$f['placeholder'] = 'Liga wählen';
		}
		if ( isset( $f['fields'] ) && is_array( $f['fields'] ) ) {
			fck_cmb_process_fields( $f['fields'] );
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
		fck_cmb_process_fields( $g['fields'] );
		$meta_boxes[] = $g;
	}
	return $meta_boxes;
}, 20 );
