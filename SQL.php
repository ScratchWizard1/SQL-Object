<?php
/*
--------------------------------------------Objekt na prácu s databazov------------------------------------------------------
Metody:
    [Stručný popis čo metoda robí]
        [nazov medory]
            [atribut]->[V akom datovom typu je vstup] (kedy by sa mal použivať daný datový tap) || ďalšie možné vstupné datové typi
        [zapis pre použitie]

    Pripojenie na databázu:
        connect(
            "servername"->string,
            "username"->string,
            "pasword"->string,
            "dbname"->string,
            );
        $object->connect("servername","username","pasword","dbname");

    Začiatok tvorby novej tabuľky:
        newtable(
            "name"->string,
        );
        $object->newtable("name");
    
    Pridanie primary key pri tvorbe tabuľky:
        primarykey(
            "name"->string, (pri vlastnom názve) || prednastavená hodnota="id"
            data_type->boolean, (pri vlastnom dátovom type) || prednastavená hodnota="INT"
            auto_increment->boolean, prednastavená hodnota=true
        );
        $object->newtable("name")->primarykey()->create();
        $object->newtable("name")->primarykey("name","data_type",auto_increment)->create();
    
    Pridanie foreign key pri tvorbe tabuľky:
        foreignkey(
            "name"->string,
            "data_type"->string, prednastavená hodnota="INT"
            "referencetable"->string,
            "referencecolumn"->string,
        $object->newtable("name")->foreignkey("name","data_type","referencetable","referencecolumn")->create();

    Pridanie stĺpca pri tvorbe tabuľky alebo pridanie stĺpca do už existujucej tabuľky:
        column(
            "name"->string,
            "data_type"->string,
            NotNull->boolean, prednastavená hodnota=false (hodnoty môžu byť NULL)
            Unique->boolean, prednastavená hodnota=false
            "default"->string, prednastavená hodnota=""
            "addtoexisttable"->string, (pridá daný stĺpec do existujucej tabuľky) || prednastavená hodnota=""
        );
        $object->newtable("name")->column("name","data_type")->create();
        $object->newtable("name")->column("name","data_type",NotNull, Unique, "default")->create();
        $object->column("name","data_type",NotNull, Unique, "default","addtoexisttable");
    
    Ziskanie kódu zostavenej tabuľky bez jej vytvorenia, vracia string s codom pre tabuľku:
        getNewTable();
        echo $object->getNewTable();

    Vytvorenie novej tabuľky:
        create();
        $object->newtable("name")->column("name","data_type")->create();
    
    Úplné a nenávratné odstranenie tabuľky aj napriek foreign key:
        droptable(
            "nazov"->string,
        );
        $object->droptable("name");

    Vloženie údajov do tabuľky:
        insert(
            "table"g->string,
            [column]->array, (pre 2 a viac stlpcov) || "column"->string (pre jeden stlpec)
            [data]->array, (pre vloženie viacerých údajov do jedneho riadka) || "column"->string (pre jeden údaj) || [[data]]-> 2D array (pre vloženie viac riadkov)
            );
        $object->insert("table",[column],[data]);

    Spustenie selectu vracia array alebo 2D array pri viac riadkoch:
        runQuery();
        $values = $object->select()->runQuery();
    
    Ziskanie zostaveného selectu bez jeho spustenia, vracia string so selectom:
        getQuery();
        echo $object->getQuery();

    Zakladný select:
        select(
            "table"->string,
            [column]->array, (pre 2 a viac stlpcov) || "column"->string (pre jeden stlpec) || prednastavená hodnota="*"
            "AGG"->string, (pre pridanie agregačných funkcii k už daným stlpcom (viď nižšie)) || prednastavená hodnota=""
        );
        $values = $object->select("table")->runQuery();
        $values = $object->select("table",[column])->runQuery();
        $values = $object->select("table",[column],"AGG")->runQuery();
    
    Pridavanie podmienok pre select pri opakovanom použiti v jednom selecte sa použije spojka AND:
        where(
            "column"->string, 
            "operator"->string,
            "value"->string, (pre string) || value->int (pre čislo)
            "logical_operator"->string, (logické operatorý: AND, OR, NOT, ORNOT) || prednastavená hodnota="AND"
        );
        $values = $object->select()->where("column","operator","value")->runQuery();
        $values = $object->select()->where("column","operator","value")->where("column","operator","value")->runQuery();
        $values = $object->select()->where("column","operator","value","logical_operator")->where("column","operator","value","logical_operator")->runQuery();

    Zoradenie výsledkov selectu:
        order(
            "column"->string,
            "direction"->string (poradie zoradenia ASC- vzostupne a DESC- zostupne) || prednastavená hodnota="ASC"
        );
        $values = $object->select()->order("column")->runQuery();
        $values = $object->select()->order("column","direction")->runQuery();
    
    Agregačné funkcie: - vkladáme ako STRING buď do miesta pre stlpec (column) v select alebo ako treti parameter v select
        "MIN(column)",
        "MAX(column)",
        "COUNT(column)",
        "AVG(column)",
        "SUM(column)" 
         
    Zoskupenie riadkov, ktoré majú rovnaké hodnoty, do súhrnných riadkov, použivané pri agregačných funkciach 
    (do group ide väčšinou stlpec, ktorý nie je v agregačnej funkcii):
        group(
            "column"->string,
        );
        $values = $object->select("table",[column],"AGG")->group("column")->runQuery();

    Limit maximalne koľko udajov sa vytiahne:
        limit(
            limit->int,
        );
        $values = $object->select()->limit(2)->runQuery();


*/ 
class SQL{
    private $connect;
    private $query;
    private $newtable;

    //-----------------------------Metody pre SQL----------------------------------//
    
    //Pripojenie na databázu
    public function connect($servername,$username,$pasword,$dbname){
        $connect= new mysqli($servername,$username,$pasword,$dbname); 
        $connect->set_charset("utf8"); 
        if($connect->connect_error){
            die("Connection failed:" . $connect->connect_error);
        } else{
            $this->connect=$connect;
        }
    }

    //Začiatok tvorby novej tabuľky
    public function newtable($name){
        $this->newtable="CREATE TABLE $name";
        return $this;
    }

    //Pridanie primary key pre tabuľku
    public function primarykey($name="id",$data_type="INT",$auto_increment=true){
        $data_type=strtoupper($data_type);
        $auto_increment=$this->auto_incrementcontrol($auto_increment);
        if(strpos($this->newtable,"(")==false){
            $this->newtable.="($name $data_type $auto_increment PRIMARY KEY";
        }else{
            $this->newtable.=", $name $data_type $auto_increment PRIMARY KEY";
        }
        return $this;
    }

    //Pridavanie stlpcou pri vytvaraní novej tabuľky
    public function column($name,$data_type,$NotNull=false,$Unique=false,$default="",$addtoexisttable=""){
        $data_type=strtoupper($data_type);
        $NotNull=$this->nullcontrol($NotNull);
        $Unique=$this->uniquecontrol($Unique);
        $default=$this->defaultcontrol($default);
        if(strlen($addtoexisttable)==0){
            if(strpos($this->newtable,"(")==false){
                $this->newtable.="($name $data_type $NotNull $Unique $default";
            }else{
                $this->newtable.=", $name $data_type $NotNull $Unique $default";
            }
        }else{
            $this->connect->query("ALTER TABLE $addtoexisttable ADD $name $data_type $NotNull $Unique $default");
        }
        return $this;
    }

    //Pridavanie foreign key pri vytvaraní novej tabuľky
    public function foreignkey($name,$data_type="INT",$referencetable,$referencecolumn){
        if(strpos($this->newtable,"(")==false){
            $this->newtable.="($name $data_type, FOREIGN KEY ($name) REFERENCES $referencetable($referencecolumn)";
        }else{
            $this->newtable.=", $name $data_type, FOREIGN KEY ($name) REFERENCES $referencetable($referencecolumn)";
        }
        return $this;
    }

     //Ziskanie sql kodu k novej tabuľke
     public function getNewTable(){
        $newtable=$this->newtable.")";
        $this->newtable="";
        return $newtable;
    }

    //Vytvorenie novej tabuľky
    public function create(){
        $newtable=$this->newtable.")";
        $this->newtable="";
        $this->connect->query($newtable);
    }

    //Vymazanie tabuľky
    public function droptable($nazov){
        $this->connect->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->connect->query("DROP TABLE $nazov");
        $this->connect->query("SET FOREIGN_KEY_CHECKS = 1");
    }
    
    //Vloženie udajov do tabuľky
    public function  insert($table,$column,$data){
        $column= $this->columncontrol($column);
        $data= $this->datacontrol($data);
        mysqli_query($this->connect,"INSERT INTO $table $column VALUES $data;");
    }

    //Ziskanie selectu
    public function getQuery(){
        $query=$this->query;
        $this->query="";
        return $query;
    }
    
    //Spustenie vyberania udajov z tabuľky
    public function runQuery(){
        $rows=[];
        $object_mysqli_result = $this->connect->query($this->query);
        $this->query="";
        if ($object_mysqli_result->num_rows==1){
            if ($object_mysqli_result->field_count!=1){
                return $object_mysqli_result->fetch_assoc();
            } else{
                $str=$this->arraytostringdata($object_mysqli_result->fetch_assoc());
                $str=substr($str, 2, -2);
                return $str;
            }
        } else{
            for($i=0;$i<($object_mysqli_result->num_rows);$i++){
                array_push( $rows, $object_mysqli_result->fetch_assoc() );
            }
            return $rows;
        }
    }

    //Vyberanie udajov z tabuľky
    public function select($table,$columns="*",$AGG=""){
        $column= $this->columncontrol($columns);
        $column=substr(substr($column,0,-1),1,);
        if($column=="*" && $AGG!=""){
            $this->query="SELECT $AGG FROM $table";
        } else if($AGG!=""){
            $this->query="SELECT $column,$AGG FROM $table";
        } else{
            $this->query="SELECT $column FROM $table";
        }
        return $this;
    }

    //Podmienky pri vyberani udajov z tabuľky musí byť po select()
    public function where($column,$operator,$value,$logical_operator="AND"){
        $value=$this->datacontrol($value);
        if(strpos($this->query,"WHERE")!==false){
            if (strtoupper($logical_operator)=="AND"){
                $this->query.= " AND $column $operator $value";
            } else if (strtoupper($logical_operator)== "OR"){
                $this->query.= " OR $column $operator $value";
            } else if (strtoupper($logical_operator)== "NOT"){
                $this->query.= " AND NOT $column $operator $value";
            } else if (strtoupper($logical_operator)== "ORNOT"){
                $this->query.= " OR NOT $column $operator $value";
            }
        } else{
            if(strtoupper($logical_operator)== "NOT"){
                $this->query.= " WHERE NOT $column $operator $value";
            } else{
                $this->query.= " WHERE $column $operator $value";
            }
        }
        return $this;
    }

    //Zoradenie vysledkov pri select
    public function order($column,$direction= "ASC"){
        $direction=strtoupper($direction);
        if ($direction=="ASC"){
            $this->query.= " ORDER BY $column ASC";
        } else if ($direction== "DESC"){
            $this->query.= " ORDER BY $column DESC";
        }
        return $this;
    }

    //Zoskupiť podľa stlpca
    public function group($column){
        $this->query.=" GROUP BY $column";
        return $this;
    }

    //Maximalné koľko udajov sa vypíše pre select
    public function limit($limit){
        $this->query.=" LIMIT $limit";
        return $this;
    }

    //---------------Metody, ktoré možu použivať potomkovia objektu SQL---------------------------//
    
    //Zisťovanie, ako treba upraviť data aby boli v tvare pre sql
    //Vstup môže byť string, array a 2D array
    //Vracia string
    protected function datacontrol($data){
        if(is_numeric($data)){
            return $data;
        }
        if (is_string($data)){
            return "('".$data."')";
        } else{
            if(is_array($data[0])){
                return $this->array2tostringdata($data);
            } else{
                return $this->arraytostringdata($data);

            }
        }
    }
    
    //Zisťovanie, ako treba upraviť stlpce aby boli v tvare pre sql
    //Vstup môže byť string a array
    //Vracia string
    protected function columncontrol($data){
        if (is_string($data)){
            return "(".$data.")";
        } else{
            return $this->arraytostringcolumn($data);
        }
    }

    //-----------------Podporné metody pre správne fungovanie objektu SQL-----------------------------//

    //Uprava arrayu s datami, do tvaru pre sql
    private function arraytostringdata($datas){
        $str="";
        foreach ($datas as $data){
            if (is_numeric($data)){
                $str.=$data. ",";
            } else{
                $str.="'". $data. "',";
            }
        }
        $str=substr($str,0,-1);
        return "(".$str.")";
    }

    //Uprava 2D arrayu s datami, do tvaru pre sql
    private function array2tostringdata($datas){
        $str="";
        foreach ($datas as $data){
            $str.=$this->arraytostringdata($data). ",";
        }
        $str=substr($str,0,-1);
        return $str;
    }

    //Uprava arrayu so stlpcami, do tvaru pre sql
    private function arraytostringcolumn($datas){
        $str="";
        foreach ($datas as $data){
            $str.=$data. ",";
        }
        $str=substr($str,0,-1);
        return "(".$str.")";
    }

    //Zisťovanie či je povolená hodnota NULL v stlpcoch
    private function nullcontrol($NotNull){
        if($NotNull==false){
            return "NULL";
        } else{
            return "NOT NULL";
        }
    }

    //Zisťovanie či je povolené unique v stlpcoch
    private function uniquecontrol($Unique){
        if($Unique==true){
            return "UNIQUE";
        } else{
            return "";
        }
    }

    //Zisťovanie či je zadaná defautná hodnota pre stlpec
    private function defaultcontrol($default){
        if(strlen($default)>0){
            return "DEFAULT '$default'";
        } else{
            return "";
        }
    }
    
    //Zisťovanie či je zapnutý auto increment pri primary key
    private function auto_incrementcontrol($auto_increment){
        if($auto_increment==false){
            return "";
        } else{
            return "AUTO_INCREMENT";
        }
    }

}

//Moje testovanie
$db = new SQL();
$db->connect("zenit.ta.sk","zenit11","cRe9JC2P","zenit11");



?> 