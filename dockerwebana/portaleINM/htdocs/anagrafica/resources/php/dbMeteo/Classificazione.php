<?php

class Classificazione extends GenericEntity{
    
    function __construct(){
        $this->DBTable = 'A_Classificazione';
        $this->IDfield = 'IDclasse';
        parent::__construct();
    }
    
    public function printListTable(){
        $output = '<thead>
                            <tr>
                                <th id="colonna_Classificazione">Identificativo</th>
                                <th id="colonna_IdClassificazione">Classificazione</th>
                            </tr>
                        </thead>';
        $output .= '<tbody>';
        foreach($this->List as $record) {
            $output .= '<tr class="recordLista">
                                    <td><b class="idEntita">' . (isset($record['IDclasse']) ? $record['IDclasse'] : '') . '</b></td>
                                    <td>' . (isset($record['Descrizione']) ? $record['Descrizione'] : '') . '</td>
                                </tr>';
        }
        $output .= '</tbody>';
        return $output;
    }
    
    
    public static function dropdownListClassificazione($listD, $selectedItem){
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
