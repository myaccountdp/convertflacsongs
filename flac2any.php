<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>flac2anyconvert</title>
</head>
<frameset>
	<frame>
	<frame>
	<noframes>
		<body>
			<p>This page uses frames. The current browser you are using does not
				support frames.</p>
    <?php
				/*
				 * IDEE: Der quell-Ordner representiert eine CD/DVD, deren *.flac nach *.mp3/4 codiert werden.
				 * Der Name des Quellordners wird übernommen, z. B. ein .mp3 als Convertziel wird angehangen
				 * In der ersten Ebene sind die Künstler, dann die Alben, dann die Titel.mp3 ...
				 */
				define ( POST_TYPE4PLAY, 0 );
				define ( POST_ARCHIVE, 1);
				$_POST [POST_ARCHIVE] = "flac";
				$_POST [POST_TYPE4PLAY] = "mp3";
				include_once 'flacfunctions.php';
				$s_dvdname = 0;
				$r_quelldvd = 0;
				// Die DVD Generierung erfolgt in den besthenden Ordnern: ~/qelldvd und ~/zieldvd
				// erwartete Struktur: ...../DVD_mit Flac/Künstler 1...m/Album 1...m/song 1... x.flac
				// erzeugte Struktur_ ...../DVD_mit Flac/Künstler 1...m/Album 1...m/song 1... x.mp3,4
				// die Konvertierenung erfolgt mit "ffmpeg -i x.flac x-mp3"
				$s_quelldvd = "/home/dieter/Musik"; //nicht den DVD-Namen angeben! 
				$a_dvdstructur = [ 
						0 
				];
				$a_recursivdir = [ 
						0 
				];
				/* $s_quelldvd = "/media/dieter/collectionx"; */
				// s_2destdvd: im Ordner ./converted wird die DVD angelegt, wenn der Name bekannt.
				$s_zieldvdordner = "/home/dieter/Schreibtisch/mp3_collection1";
				$s_2format = "mp3"; /* Konvertierungsvorgabe */
				
				// gibt es das Verzeichnis für die zu convertierenden DVD' schon?
				if (! is_dir ( $s_zieldvdordner )) {
					/* Wenn nicht muss deer Zielordner angelegt werden */
					if (! mkdir ( $s_zieldvdordner, 0755, true )) {
						die ( 'Erstellung der Ziel-DVD-Verzeichnisses schlug fehl...\n' );
					}
				}
				// gibt es den DVD-Ordner: "dvdconverted" . $s_2format schon?
				$s_dvd = $s_zieldvdordner . "/DVD_in_" . $_POST [POST_TYPE4PLAY];
				
				/*
				 * **********************************************************
				 * diese Zeile nur in der Entwicklung benutzen
				 */
				exec ( 'rm -d -r /home/dieter/Schreibtisch/mp3_collection1' );
				/*
				 * ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				 */
				if (! is_dir ( $s_dvd )) {
					if (! mkdir ( $s_dvd, 0755, true )) {
						die ( "Erstellung des Ziel-DVD-Verzeichnisses schlug fehl...\n" );
					}
				} else {
					die ( "Zielordner leeren..." );
				}
				/* Zielordner ~/zieldvd/DVD_in_xxx angelegen */
				
				// test ... sCHREIBTISCH/aflaccollection5 ERSTER oRDNER DER sOURCE!
				// .mp3/aflaccollection5 fehlt noch, ALSO DER nAME DES oRDNERS/DER dvd VON SOURCE
				// SOURCEDVD IST LETZTER tEIL VON üBERGABEPARAM...
				
				if ($r_quelldvd = opendir ( $s_quelldvd ) or die ( "keine Flac-DVD" . $s_quelldvd . "gefunden!\n" )) {
					// Im QUellordner soll genau ein DVD-Verrzeichnis sein!
					$a_zdir = array ();
					$n_i = 0;
					while ( $s_dirs = readdir ( $r_quelldvd ) ) {
						array_push ( $a_zdir, $s_dirs );
						$n_i ++;
					}
					if ($n_i != 3)
						die ( "im dvd-Qellordner ist nicht genau eine DVD! " );
					// aus dem Array den Namen der DVD extrahieren ... und zieldirectorie anlegen
					$n_i = 0;
					while ( $a_zdir [$n_i] == '.' || $a_zdir [$n_i] == ".." )
						$n_i ++;
					$s_dvdname = $a_zdir [$n_i];
					if (! mkdir ( $s_dev = $s_dvd . "/" . $s_dvdname, 0755, true )) {
						die ( "Ordner für Ziel-DVDkonnte nicht angelegt werden...\n" );
					}
					// mkdir $s_dvd . "/" . aflaccollection5
					$a_recursivdir = pc_process_dir ( $s_quelldvd . "/" . $s_dvdname, $s_dev );
					$a_newdir = explode ( $s_quelldvd, $a_recursivdir [0] );
					$a_path = pathinfo ( $a_newdir [1] );
					$a_dvdstructur = scandir ( $s_quelldvd );
					$n_i = 0;
					while ( $a_dvdstructur [$n_i] ) {
						if (! (($a_dvdstructur [$n_i]) == "." || ($a_dvdstructur [$n_i] == ".."))) {
							echo "$a_dvdstructur[$n_i] \n";
						}
						$n_i ++;
					}
				}
				while ( $s_dvditem = readdir ( $r_quelldvd ) ) {
					$s_aktitem = $s_quelldvd . "/" . $s_dvditem;
					if (is_dir ( $s_aktitem )) {
						echo "ordner erstellen, sofern nicht . oder nicht ..";
						if (! ($s_dvditem == '.' || $s_dvditem == '..')) {
							echo "den IN SOURCE gefundenen ordner ind dest erstellen: ";
							if (! mkdir ( $s_dvd . "/" . $s_dvditem, 0755, true )) {
								die ( 'Erstellung der Verzeichnisse schlug fehl...\n' );
							}
							echo "der Ordner wurde erstellt\n";
							echo "rekursiv in den parallel-Ordner gehen und ...";
						}
					}
				}
				echo "ENDE";
				?>
    </body>
	</noframes>
</frameset>
</html>



