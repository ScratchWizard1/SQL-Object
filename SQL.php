<?php
/*
--------------------------------------------Objekt na prácu s databazov------------------------------------------------------
Metody:
    [Stručný popis čo metoda robí]
        [nazov metódy]
            [Atribút]->[V akom datovom typeje vstup] (kedy by sa mal použivať daný datový typ) || ďalšie možné vstupné datové typy
        [zapis pre použitie]

    Pripojenie na databázu:
        connect(
            "servername"->string,
            "username"->string,
            "pasword"->string,
            "dbname"->string,
            );
        SQL::connect("servername","username","pasword","dbname");
        $object->connect("servername","username","pasword","dbname");

    Začiatok tvorby novej tabuľky:
        newtable(
            "name"->string,
        );
        SQL::newtable("name")
        $object->newtable("name");
    
    Pridanie primary key pri tvorbe tabuľky:
        primarykey(
            "name"->string, (pri vlastnom názve) || prednastavená hodnota="id"
            data_type->boolean, (pri vlastnom dátovom type) || prednastavená hodnota="INT"
            auto_increment->boolean, prednastavená hodnota=true
        );
        newtable("name")->primarykey();
        newtable("name")->primarykey("name","data_type",auto_increment);
    
    Pridanie foreign key pri tvorbe tabuľky:
        foreignkey(
            "name"->string,
            "data_type"->string, prednastavená hodnota="INT"
            "referencetable"->string,
            "referencecolumn"->string,
        newtable("name")->foreignkey("name","data_type","referencetable","referencecolumn");

    Pridanie stĺpca pri tvorbe tabuľky:
        column(
            "name"->string,
            "data_type"->string,
            NotNull->boolean, prednastavená hodnota=false (hodnoty môžu byť NULL)
            Unique->boolean, prednastavená hodnota=false
            "default"->string, prednastavená hodnota=""
        );
        newtable("name")->column("name","data_type")->create();
        newtable("name")->column("name","data_type",NotNull, Unique, "default")->create();

    Pridanie stĺpca do už existujucej tabuľky:
        addcolumn(
            "table"->string,
            "name"->string,
            "data_type"->string,
            NotNull->boolean, prednastavená hodnota=false (hodnoty môžu byť NULL)
            Unique->boolean, prednastavená hodnota=false
            "default"->string, prednastavená hodnota=""
        );
        SQL::addcolumn("table",name","data_type");
        

    
    Ziskanie kódu zostavenej tabuľky bez jej vytvorenia, vracia string s codom pre tabuľku:
        showNewTable();
        echo SQL::newtable("name")->column("name","data_type")->showNewTable();

    Vytvorenie novej tabuľky:
        create();
        SQL::newtable("name")->column("name","data_type")->create();
    
    Úplné a nenávratné odstranenie tabuľky aj napriek foreign key:
        droptable(
            "nazov"->string,
        );
        SQL::droptable("name");

    Vloženie údajov do tabuľky:
        insert(
            "table"g->string,
            [column]->array, (pre 2 a viac stlpcov) || "column"->string (pre jeden stlpec)
            [data]->array, (pre vloženie viacerých údajov do jedneho riadka) || "column"->string (pre jeden údaj) || [[data]]-> 2D array (pre vloženie viac riadkov)
            );
        SQL::insert("table",[column],[data]);

    Spustenie selectu vracia array alebo 2D array pri viac riadkoch:
        getQuery();
        $values = SQL::select()->getQuery();
    
    Ziskanie zostaveného selectu bez jeho spustenia, vracia string so selectom:
        showQuery();
        echo SQL::select()->showQuery();

    Zakladný select:
        select(
            "table"->string,
            [column]->array, (pre 2 a viac stlpcov) || "column"->string (pre jeden stlpec) || prednastavená hodnota="*"
            "AGG"->string, (pre pridanie agregačných funkcii k už daným stlpcom (viď nižšie)) || prednastavená hodnota=""
        );
        $values = SQL::select("table")->getQuery();
        $values = SQL::select("table",[column])->getQuery();
        $values = SQL::select("table",[column],"AGG")->getQuery();
    
    Pridavanie joinu do selectu:
        join(
                "from_table"->string, prednastavená hodnota=tabuľka v selecte
                "$from_column"->string,
                "to_table"->string,
                "to_column"->string,
                "method"->string, prednastavená hodnota="INNER" ("INNER","RIGHT","LEFT")
            );
        select("table")->join("","from_column","to_table","to_column")->getQuery();
        select("table")->join("from_table","from_column","to_table","to_column")->getQuery();
        select("table")->join("from_table","from_column","to_table","to_column","method")->getQuery();
    
    Pridavanie full joinu do selectu:
    fulljoin(
            "to_table"->string,
            );
        select("table")->fulljoin("to_table")->getQuery();

    
    Pridavanie podmienok pre select pri opakovanom použiti v jednom selecte sa použije nastavený logicky operator:
        where(
            "column"->string, 
            "value"->string, (pre string) || value->int (pre čislo)
            "operator"->string, (operatorý: =, >, <, != ) || prednastavená hodnota="="
            "logical_operator"->string, (logické operatorý: AND, OR, NOT, ORNOT) || prednastavená hodnota="AND"
        );
        select()->where("column","operator","value")->getQuery();
        select()->where("column","operator","value")->where("column","operator","value")->getQuery();
        select()->where("column","operator","value","logical_operator")->where("column","operator","value","logical_operator")->getQuery();

    Zoradenie výsledkov selectu:
        order(
            "column"->string,
            "direction"->string (poradie zoradenia ASC- vzostupne a DESC- zostupne) || prednastavená hodnota="ASC"
        );
        select()->order("column")->getQuery();
        select()->order("column","direction")->getQuery();
    
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
        select("table",[column],"AGG")->group("column")->getQuery();

    Limit maximalne koľko udajov sa vytiahne:
        limit(
            limit->int,
        );
        select()->limit(2)->getQuery();


*/ 
class SQL{
    private static $connect;
    private $query;
    private $newtable;

    //-----------------------------Metody pre SQL----------------------------------//
    
    //Pripojenie na databázu
    public static function connect($servername,$username,$pasword,$dbname){
        $connect= new mysqli($servername,$username,$pasword,$dbname); 
        $connect->set_charset("utf8"); 
        if($connect->connect_error){
            die("Connection failed:" . $connect->connect_error);
        } else{
            self::$connect=$connect;
        }
    }

    //Začiatok tvorby novej tabuľky
    public static function newtable($name){
        $inst= new self();
        $inst->newtable="CREATE TABLE $name";
        return $inst;
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
    public  function column($name,$data_type,$NotNull=false,$Unique=false,$default=""){      
        $data_type=strtoupper($data_type);
        $NotNull=$this->nullcontrol($NotNull);
        $Unique=$this->uniquecontrol($Unique);
        $default=$this->defaultcontrol($default);
        if(strpos($this->newtable,"(")==false){
            $this->newtable.="($name $data_type $NotNull $Unique $default";
        }else{
            $this->newtable.=", $name $data_type $NotNull $Unique $default";
        }
        return $this;
    }
    public static  function addcolumn($table,$name,$data_type,$NotNull=false,$Unique=false,$default=""){      
        $data_type=strtoupper($data_type);
        $inst = new self();
        $NotNull=$inst->nullcontrol($NotNull);
        $Unique=$inst->uniquecontrol($Unique);
        $default=$inst->defaultcontrol($default);
        self::$connect->query("ALTER TABLE $table ADD $name $data_type $NotNull $Unique $default");
    }

    //Pridavanie foreign key pri vytvaraní novej tabuľky
    public function foreignkey($name,$referencetable,$referencecolumn,$data_type="INT"){
        if(strpos($this->newtable,"(")==false){
            $this->newtable.="($name $data_type, FOREIGN KEY ($name) REFERENCES $referencetable($referencecolumn)";
        }else{
            $this->newtable.=", $name $data_type, FOREIGN KEY ($name) REFERENCES $referencetable($referencecolumn)";
        }
        return $this;
    }

     //Ziskanie sql kodu k novej tabuľke
     public function showNewTable(){
        $newtable=$this->newtable.")";
        $this->newtable="";
        return $newtable;
    }

    //Vytvorenie novej tabuľky
    public function create(){
        $newtable=$this->newtable.")";
        $this->newtable="";
        self::$connect->query($newtable);
    }

    //Vymazanie tabuľky
    public static function droptable($nazov){
        self::$connect->query("SET FOREIGN_KEY_CHECKS = 0");
        self::$connect->query("DROP TABLE $nazov");
        self::$connect->query("SET FOREIGN_KEY_CHECKS = 1");
    }
    
    //Vloženie udajov do tabuľky
    public static function  insert($table,$column,$data){
        $inst = new self();
        $column= $inst->columncontrol($column);
        if (!is_numeric($data)){
            if (is_array($data[0])){
                $data= $inst->datacontrol($data);
            } else{
                $data= $inst->datacontrol($data);
                $data= $inst->insert_control($data);
            }
        }else{
            $data="(".$data.")";
        }
        mysqli_query(self::$connect,"INSERT INTO $table $column VALUES $data;");
    }

    //Ziskanie selectu
    public function showQuery(){
        return $this->query;
    }
    
    //Spustenie vyberania udajov z tabuľky
    public function getQuery(){
        $rows=[];
        $object_mysqli_result = self::$connect->query($this->query);
        $this->query="";
        if ($object_mysqli_result->num_rows==1){
            if ($object_mysqli_result->field_count!=1){
                return $object_mysqli_result->fetch_assoc();
            } else{
                foreach ($object_mysqli_result->fetch_assoc() as $key => $value){
                    if (!is_numeric($value)){
                        $str=$this->arraytostringdata($object_mysqli_result->fetch_assoc());
                        $str=substr($str, 2, -2);
                        return $str;
                    } else {
                        return $value;
                    }
                }
            }
        } else{
            for($i=0;$i<($object_mysqli_result->num_rows);$i++){
                array_push( $rows, $object_mysqli_result->fetch_assoc() );
            }
            return $rows;
        }
    }

    //Vyberanie udajov z tabuľky
    public static function select($table,$columns="*",$AGG=""){
        $inst= new self();
        $column= $inst->columncontrol($columns);
        $column=substr(substr($column,0,-1),1,);
        if($column=="*" && $AGG!=""){
            $inst->query="SELECT $AGG FROM $table";
        } else if($AGG!=""){
            $inst->query="SELECT $column,$AGG FROM $table";
        } else{
            $inst->query="SELECT $column FROM $table";
        }
        return $inst;
    } 
    //Pridanie Joinu do selectu
    public function join($from_table="this_table",$from_column,$to_table,$to_column,$method="INNER"){
        $from_table=$this->tablecontrol($from_table);
        $method=$this->method_coltrol($method);
        $this->query.=" $method JOIN $to_table ON ".$from_table.".$from_column = $to_table.$to_column";
        return $this;
    }
    //Pridanie Full Joinu do selectu
    public function fulljoin($to_table){
        $this->query.=" FULL JOIN $to_table";
        return $this;
    }

    //Podmienky pri vyberani udajov z tabuľky musí byť po select()
    public function where($column,$value,$operator="=",$logical_operator="AND"){
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

    //uprava insert value dat
    private function insert_control($datas){
        $datas=substr($datas,0,-1);
        $datas=substr($datas,1);
        $data=explode(",",$datas);
        $str="";
        foreach($data as $word){
            $str.="($word),";
        }
        $str=substr($str,0,-1);
        return $str;
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

    //Ziskanie akutalnej tabuľky z Query
    private function from_table(){
        $check = explode(" ",self::showQuery());
        for($i=0;$i<count($check);$i++){
            if ($check[$i]=="FROM"){
                return $check[$i+1];
            }
        }
    }
    //Method coltrol
    private function method_coltrol($method){
        $method=strtoupper($method);
        switch($method){
            case "LEFT":
                return "LEFT";
            case "RIGHT":
                return "RIGHT";
            default:
                return "INNER";
        }
    }

    //Zistovanie from tabuľky v joine
    private function tablecontrol($table){
        $table=str_replace(" ", "", $table);
        if ($table=="this_table"){
            return $this->from_table();
        }else if($table==""){
            return $this->from_table();
        } else{
            return $table;
        }
    }

}

//Moje testovanie
// SQL::connect("localhost","root","","r3-prax-sql-object");
// print_r(SQL::select("pokus")->getQuery());
?> 
