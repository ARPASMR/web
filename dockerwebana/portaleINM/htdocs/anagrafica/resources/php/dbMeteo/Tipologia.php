<?php

    class Tipologia extends GenericEntity{

        function __construct(){
            $this->DBTable = 'A_Tipologia';
            $this->IDfield = 'IdTipologia';
            parent::__construct();
        }

        public function printListTable(){
            $output = '<thead>
                            <tr>
                                <th id="colonna_Nome">Nome</th>
                                <th id="colonna_IdTipologia">Identificativo</th>
                                <th id="colonna_Info">Descrizione</th>
                                <th id="colonna_Importato">dati nel DBmeteo</th>
				<th id="colonna_Gruppo">Gruppo</th>
                            </tr>
                        </thead>';
            $output .= '<tbody>';
            foreach($this->List as $record) {
                $output .= '<tr class="recordLista">
                                    <td>' . (isset($record['Nome']) ? $record['Nome'] : '') . '</td>
                                    <td><b class="idEntita">' . (isset($record['IdTipologia']) ? $record['IdTipologia'] : '') . '</b></td>
                                    <td>' . (isset($record['Info']) ? $record['Info'] : '') . '</td>
                                    <td>' . (isset($record['Importato']) ? $record['Importato'] : '') . '</td>
				    <td style="background-color: ' . GruppiSensoriConverter::convertGruppoToColorHex($record['Gruppo']) . '">' . (isset($record['Gruppo']) ? $record['Gruppo'] : '') . '</td>
                                </tr>';
            }
            $output .= '</tbody>';
            return $output;
        }


        public function ottieniTabellaOsservazioni($tipoSensore){
            $tabellaDB = '';
            switch($tipoSensore){
                case "N":
                    $tabellaDB = 'm_nivometri_';
                    break;
                case "PA":
                    $tabellaDB = 'm_barometri_';
                    break;
                case "PP":
                case "PPR":
                    $tabellaDB = 'm_pluviometri_';
                    break;
                case "RG":
                    $tabellaDB = 'm_radiometrig_';
                    break;
                case "RN":
                    $tabellaDB = 'm_radiometrin_';
                    break;
                case "T":
                case "TV":
                    $tabellaDB = 'm_termometri_';
                    break;
                case "UR":
                    $tabellaDB = 'm_igrometri_';
                    break;
                case "DV":
                case "DVS":
                case "DVP":
                case "DVQ":
                    $tabellaDB = 'm_anemometridv_';
                    break;
                case "VV":
                case "VVS":
                case "VVP":
                case "VVQ":
                    $tabellaDB = 'm_anemometrivv_';
                    break;
            }
            return $tabellaDB;
        }
        
        public function formattaStringaSensoriWEB($sensori){
            $sensoriString = '';
            if(in_array('N', $sensori)){
                $sensoriString .= '';               // da completare!!
            }
            if(in_array('PA', $sensori)){
                $sensoriString .= 'P';
            }
            if(in_array('PP', $sensori) ||
                in_array('PPR', $sensori)){
                $sensoriString .= 'R';
            }
            if(in_array('RG', $sensori)){
                $sensoriString .= 'G';
            }
            if(in_array('RN', $sensori)){
                $sensoriString .= 'N';
            }
            if(in_array('T', $sensori) ||
                in_array('TV', $sensori)){
                $sensoriString .= 'T';
            }
            if(in_array('UR', $sensori)){
                $sensoriString .= 'U';
            }
            if(in_array('DV', $sensori) ||
                in_array('DVS', $sensori) ||
                in_array('DVP', $sensori) ||
                in_array('DVQ', $sensori)){
                $sensoriString .= 'D';
            }
            if(in_array('VV', $sensori) ||
                in_array('VVS', $sensori) ||
                in_array('VVP', $sensori) ||
                in_array('VVQ', $sensori)){
                $sensoriString .= 'V';
            }
            return $sensoriString;
        }

		public static function dropdownListNOMEtipologia($listD, $selectedItem){
			$colorArray = array("AGRO" => "white",
								"FIRE" => "red",
								"IDRO" => "lightblue",
								"METEO" => "yellow",
								"NIVO" => "green",
								"PRESENTE" => "pink",
								"TURBO" => "orange"
							);
			$sql = 'SELECT * FROM A_Tipologia
					ORDER BY GRUPPO, Nome';
			$DB = new DBInterface();
			$tipologie = $DB->executeQuery($sql);
			$result = '<select id="'.$listD.'" name="'.$listD.'">';
			$result .= '<option value="">- -</option>';
			foreach($tipologie as $tipologia){
				$nomeTipologia = $tipologia['Nome'];
				$result .= '<option value="'.$nomeTipologia.'" style="background-color: '.$colorArray[$tipologia['Gruppo']].'" ';
				if($nomeTipologia == $selectedItem) $result.= 'selected="selected" ';
				$result .= '>' . $nomeTipologia . '</option>';
			}
			$result .= '</select>';
			return $result;
		}

    }



    /*
     *
    Corrispondeze colonne CSV

    data_e_ora

    prec(mm/h)              m_pluviometri_YYYY      PP
    prec2(mm/h)
    urel(%)                 m_igrometri_        UR
    press(hPa)              m_barometri_        PA
    temp(C)                 m_termometri_       T
    temp2(C)                m_termometri_       T ??

    dir(gradi_da_nord)      m_anemometridv_     DVS
    dir2(gradi_da_nord)	    m_anemometridv_     DV
    vel(m/s)                m_anemometrivv_     VVS
    vel2(m/s)               m_anemometrivv_     VV
    radG(W/m^2)             m_radiometrig_      RG
    radN(W/m^2)             m_radiometrin_      RN
                            m_nivometri_


     */