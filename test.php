<?php
$_fp = fopen("php://stdin", "r");
/* Enter your code here. Read input from STDIN. Print output to STDOUT */
fscanf($_fp, "%d %d %d", $N, $M, $K);
$type_nos = array();
for($i=0; $i<$N; $i++){
    $ftn = fgets($_fp);
    $ftn = explode(" ", $ftn);
    $type_nos[$i] = $ftn;
}
$paths_w = array();
for($i=0; $i<$M; $i++){
    $ftn = fgets($_fp);
    $ftn = explode(" ", $ftn);
    $paths_w[$ftn[0]][] = array($ftn[1], $ftn[2]);
    $paths_w[$ftn[1]][] = array($ftn[0], $ftn[2]);
}
$complete = false;
$big_cat_t = array();
$small_cat_t = array();
$big_cat_t["1"] = 1;
$small_cat_t["1"] = 1;
$bc_last = 1;
$sc_last = 1;
$big_cat_c = array();
$small_cat_c = array();
$fish_c = array();
$big_time = 0;
$small_time = 0;
$count = 0;

$start_f_nos = $type_nos[($bc_last-1)][0];
for($i=1; $i<=$start_f_nos; $i++){
    $fish_c[$type_nos[($bc_last-1)][$i]] = true;
}

do{
    
    //big cat
    if(isset($paths_w[$bc_last])){
        $conn = $paths_w[$bc_last];
        $tot_paths = count($conn);
        $min_t = 0;
        $min_t_s = 0;
        for($i=0; $i<$tot_paths; $i++){
            $next_shop = $conn[$i][0];
            $next_t = intval(trim($conn[$i][1]));
            if($min_t!=0){
                if($min_t>$next_t){
                    $min_t=$next_t;
                    $min_t_s = $next_shop;
                }
            }else{
                $min_t=$next_t;
                $min_t_s = $next_shop;
            }
            $no_f = $type_nos[($next_shop-1)][0];
            for($j=1; $j<=$no_f; $j++){
                $fish_type = $type_nos[($next_shop-1)][$j];
                if(!isset($fish_c[$fish_type])){
                    $fish_c[$fish_type] = true;
                    $bc_last = $next_shop;
                    $big_time += $next_t;
                    $big_cat_t[] = $next_shop;
                    break 2;
                }else if(isset($fish_c[$fish_type]) && $next_shop==$N){
                    $bc_last = $next_shop;
                    $big_time += $next_t;
                    $big_cat_t[] = $next_shop;
                    break 2;
                }else if(isset($fish_c[$fish_type]) && $i==($tot_paths-1)){
                    $bc_last = $min_t_s;
                    $big_time += $min_t;
                    $big_cat_t[] = $min_t_s;
                    break 2;
                }
            }
        }
        //echo $big_time.PHP_EOL;
    }
    //small cat
    if(isset($paths_w[$sc_last])){
        $conn = $paths_w[$sc_last];
        $tot_paths = count($conn);
        $min_t = 0;
        $min_t_s = 0;
        for($i=0; $i<$tot_paths; $i++){
            $next_shop = $conn[$i][0];
            $next_t = $conn[$i][1];
            if($min_t!=0){
                if($min_t>$next_t){
                    $min_t=$next_t;
                    $min_t_s = $next_shop;
                }
            }else{
                $min_t=$next_t;
                $min_t_s = $next_shop;
            }
            $no_f = $type_nos[($next_shop-1)][0];
            for($j=1; $j<=$no_f; $j++){
                $fish_type = $type_nos[($next_shop-1)][$j];
                if(!isset($fish_c[$fish_type])){
                    $fish_c[$fish_type] = true;
                    $sc_last = $next_shop;
                    $small_time += $next_t;
                    $small_cat_t[] = $next_shop;
                    break 2;
                }else if(isset($fish_c[$fish_type]) && $next_shop==$N){
                    $sc_last = $next_shop;
                    $small_time += $next_t;
                    $small_cat_t[] = $next_shop;
                    break 2;
                }else if(isset($fish_c[$fish_type]) && $i==($tot_paths-1)){
                    $sc_last = $min_t_s;
                    $small_time += $min_t;
                    $small_cat_t[] = $min_t_s;
                    break 2;
                }
            }
        }
    }
    echo "big";
    print_r($big_cat_t);
    echo "small";
    print_r($small_cat_t);
    echo "fish";
    print_r($fish_c);
    if(($K == count($fish_c)) && ($bc_last==$N) && ($sc_last==$N)){
        $complete = true;
    }
}while(!$complete);
//print_r($fish_c);
if($big_time!=$small_time){
    echo max($big_time, $small_time);
}else{
    echo $big_time;
}
?>