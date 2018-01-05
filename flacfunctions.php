<?php
/* s. php Kochbuch S. 528 $s_quellendir:Quelle $s_teilziel:Ziel */
function pc_process_dir($s_quellendir, $s_teilziel, $max_depth = 5, $depth = 0) {
	if ($depth >= $max_depth) {
		error_log ( "Maximale Tiefe $max_depth in $s_quellendir erreicht." );
		return false;
	}
	$subdirectories = array ();
	$files = array ();
	$is = is_dir ( $s_quellendir );
	$redb = is_readable ( $s_quellendir );
	if (is_dir ( $s_quellendir ) && is_readable ( $s_quellendir )) {
		$d = dir ( $s_quellendir );
		while ( false !== ($f = $d->read ()) ) {
			// skip . und ..
			if (('.' == $f) || ('..' == $f)) {
				continue;
			}
			if (is_dir ( "$s_quellendir/$f" )) {
				array_push ( $subdirectories, "$s_quellendir/$f" );
				mkdir ( $s_teilziel . "/" . $f, 0755 ); // $dirname ist directory vor der rekursion
			} else {
				array_push ( $files, "$s_quellendir/$f" );
				$s_flacsong = $s_teilziel . "/" . $f;
				// .flac convertieren, andere copieren
				$s_pathinfo = pathinfo ( $s_flacsong,  PATHINFO_EXTENSION );
				$s_isflacextension = $s_pathinfo;
				$s_paramextension = $_POST [POST_ARCHIVE];
				$s_scratch = strcmp ($s_isflacextension, $s_paramextension);
				if (!$s_scratch)  {
					$a_ffmpeg = array(); 
					$n_ffmpegretval =0;
					$s_ffmpegquelle = $s_quellendir."/".$f;
					$s_ffmpegziel = $s_teilziel."/".$f;
					$s_2convertfile = (pathinfo($f,FILENAME));
					$s_2convertfile = $s_2convertfile . "/" . $_POST[POST_TYPE4PLAY];
					$s_ffmpeg = exec ( ('ffmpeg -i ' . escapeshellarg ($s_ffmpegquelle) . ' ' . escapeshellarg ($s_ffmpegziel)), $a_retval, $n_retval );
					if (retval) {
						die ( "konvertierung nach $_POST[ARCHFORMAT] nicht OK!\n" );
					}
				} else {

					// Das hier muß ein reguläres quell-File während test in Misik sein!!
					$s_copyfile = $s_quellendir . "/" . $f;
					if (!is_file ($s_copyfile)) { 
						die ( " hier liegt ein unbekanntes Problem vor: $s_flacsong\n" );
					}         //     $s_convertsong ist  nicht initialisiert!
					$s_copyziel = $s_teilziel."/".$f;
					$s_sratch = copy ($s_copyfile, $s_copyziel);
					if (!$s_sratch) { 
						die ( "Das File $s_copyfile läßt sich nicht nach Ziel: $s_teilziel kopieren! \n" );
					}
				}
				
				
			}
		}
		$d->close ();
		foreach ( $subdirectories as $subdirectory ) {
			// den Unterordner für Zielarray aus subdirektory isolieren...
			$a_ziel_dir = explode ( $s_quellendir, $subdirectory );
			$files = array_merge ( $files, pc_process_dir ( $subdirectory, $s_teilziel . $a_ziel_dir [1], $max_depth, $depth + 1 ) );
		}
	}
	return $files;
}
/*  noch braucgbar????
$n_count = 0; // wofür?
$s_convertsong = str_ireplace ( "flac", "", $s_flacsong, $n_count );
$s_convertsong = $s_convertsong . $_POST [POST_CONVERTTYPE];
$s_quellsong = $s_quellendir . '/' . $f;
if (! rename ( $s_quellsong, $s_convertsong )) {
	die ( "konnte den Song nicht behandeln" );
}

*/
?>