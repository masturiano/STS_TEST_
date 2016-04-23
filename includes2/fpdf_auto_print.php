<?php
	if (!class_exists('PDF_AutoPrint'))
	{
		class PDF_AutoPrint extends PDF_Javascript
		{
			function AutoPrint($dialog=false)
			{
				//Launch the print dialog or start printing immediately on the standard printer
				$param=($dialog ? 'true' : 'false');
				$script="print($param);";
				$this->IncludeJS($script);
			}
			
			function AutoPrintToPrinter($server, $printer, $dialog=true)
			{
				//Print on a shared printer (requires at least Acrobat 6)
				$script = "var pp = getPrintParams();";
				if($dialog)
					$script .= "pp.interactive = pp.constants.interactionLevel.full;";
				else
					$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
				$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
				$script .= "print(pp);";
				$this->IncludeJS($script);
			}
		}
	}
?>
