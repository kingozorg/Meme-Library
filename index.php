<?php

$Start = microtime(true);
$Path = './';
global $FQDNURL, $Tags, $Filter;
$FQDNURL = 'https://cjtrowbridge.com/memes';
$IgnoredExtensions = array();
if(
  isset($_GET['f'])&&
  ($_GET['f'] != 'All')
){
  $Filter = $_GET['f'];
}else{
  $Filter = false;
  $_GET['f'] = 'All';
}
$Tags = array('All');

if(isset($_GET['h'])){
  $Hash = $_GET['h'];
}else{
  $Hash = false;
}

$Pics = GetFiles($Path, $FQDNURL, $Filter, $Hash);
usort($Pics, function ($item1, $item2) {
  return $item2['Time'] <=> $item1['Time'];
});
$Chunks = array_chunk($Pics,100);

if(!(isset($_GET['p']))){
  $_GET['p'] = 1;
}
$Index = $_GET['p'];
$_GET['p']--;
if(
  (intval($Index) == 0) ||
  ($Index > count($Chunks))
){
  die('Invalid page number: '.$Filter.': '.$Index.', Only found '.count($Chunks).' pages.');
}
$Index = $_GET['p'];

$Pics = $Chunks[$Index];


function GetFiles($Path, $URL, $Filter = false, $MatchHash = false){
  global $FQDNURL, $Tags;
  $Ret = array();
  if ($Handle = opendir($Path)) {
    while (false !== ($File = readdir($Handle))) {
      if (
        $File != "." && 
        $File != ".." &&
        $File != ".git" &&
        $File != ".sync" &&
        $File != ""
      ){
        if(is_dir($Path.'/'.$File)){
          $New = GetFiles($Path.'/'.$File, $URL.'/'.$File, $Filter, $MatchHash);
          foreach($New as $NewFile){
            $Ret[]=$NewFile;
          }
        }else{
            $FileExtension = strtolower(pathinfo($File, PATHINFO_EXTENSION));
            if(
              $FileExtension == 'jpg' ||
              $FileExtension == 'jpeg' ||
              $FileExtension == 'gif' ||
              $FileExtension == 'png' ||
              $FileExtension == 'bmp'
            ){
              
              $FQPath = $URL.'/'.$File;
              
              $Time = filemtime($Path.'/'.$File);
              
              $In = str_replace($FQDNURL, '', $FQPath);
              $In = str_replace($File, '', $In);
              $In = trim($In, '/');
              
              $Hash = md5($File.$Time); //This is just a unique identifier for the file in case it is moved between folders so it will still work.
                
              if($In != ""){
                $Tags[$In] = $In;
              }
              if(
                ($Filter == false) ||
                ($Filter == $In)
              ){
                $New = array(
                  'Time' => $Time,
                  'URL' => $FQPath,
                  'In' => $In,
                  'Hash' => $Hash
                );
                
                if(
                  ($MatchHash == false) ||
                  ($Hash == $MatchHash)
                ){
                  $Ret[] = $New;
                }
              }
            }else{
              $IgnoredExtensions[$FileExtension] = $FileExtension;
            }
        }
      }
    }
    closedir($Handle);
  }
  return $Ret;
}

function ago($time){
  /*
    Ago accepts any date or time and returns a string explaining how long ago that was.
    For example, "6 days ago" or "8 seconds ago"
  */
  if(intval($time)==0){
    $time=strtotime($time);
  }
  if(($time==0)||(empty($time))){
    return 'Never';
  }
  $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
  $lengths = array("60","60","24","7","4.35","12","10");
  $now = time();
  $difference     = $now - $time;
  $tense         = "ago";
  for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
    $difference /= $lengths[$j];
  }
  $difference = round($difference);
  if($difference != 1) {
    $periods[$j].= "s";
  }
  return "$difference $periods[$j] ago";
}

?><!DOCTYPE html>
<html>
<head>
  <title>👁👄👁 - CJ Trowbridge</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  
  <meta property="og:url"                content="https://cjtrowbridge.com/memes/" />
  <meta property="og:type"               content="website" />
  <meta property="og:title"              content="My Meme Library" />
  <meta property="og:description"        content="For years I have saved all the best memes I've seen and categorized them by topic. Recently I decided to make this archive publicly available. Check it out, I'm sure you will find something you enjoy." />
  <meta property="og:image"              content="https://cjtrowbridge.com/memes/Meta/90944353_10219582728670541_7843477457867898880_o.jpg" />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="icon" type="image/jpg" href="/cj.jpg">
  <style>
    img{
      width: 100%;
      max-width: 100%;
      border-radius: 1em;
      margin-bottom: 1.5rem;
    }
    body{
      background-color: #F8F8F8;
      padding-top: 2rem;
    }
  </style>
</head>
<body>
  
<div class="container">
  <div class="row">
    <div class="col-12">
      
      <h1 class="mt-2"><a href="./">My Memetic Library</a><?php
        if(
          (isset($_GET['f']))&&
          ($_GET['f'] != 'All')
        ){
          echo ': '.$_GET['f'];
        }
      ?></h1>
      <p><style>.bmc-button img{height: 34px !important;width: 35px !important;margin-bottom: 1px !important;box-shadow: none !important;border: none !important;vertical-align: middle !important;}.bmc-button{padding: 7px 10px 7px 10px !important;line-height: 35px !important;height:51px !important;min-width:217px !important;text-decoration: none !important;display:inline-flex !important;color:#FFFFFF !important;background-color:#FF813F !important;border-radius: 5px !important;border: 1px solid transparent !important;padding: 7px 10px 7px 10px !important;font-size: 20px !important;letter-spacing:-0.08px !important;box-shadow: 0px 1px 2px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5) !important;margin: 0 auto !important;font-family:'Lato', sans-serif !important;-webkit-box-sizing: border-box !important;box-sizing: border-box !important;-o-transition: 0.3s all linear !important;-webkit-transition: 0.3s all linear !important;-moz-transition: 0.3s all linear !important;-ms-transition: 0.3s all linear !important;transition: 0.3s all linear !important;}.bmc-button:hover, .bmc-button:active, .bmc-button:focus {-webkit-box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5) !important;text-decoration: none !important;box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5) !important;opacity: 0.85 !important;color:#FFFFFF !important;}</style><link href="https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext" rel="stylesheet"><a class="bmc-button" target="_blank" href="https://www.buymeacoffee.com/cjtrowbridge"><img src="https://cdn.buymeacoffee.com/buttons/bmc-new-btn-logo.svg" alt="Buy me a coffee"><span style="margin-left:15px;font-size:19px !important;">Buy me a coffee</span></a></p>
      <p><b>CAVEAT EMPTOR:</b> This is all the memes and content I choose to save. Some of it I agree with. Some of it I specifically disagree with. For example I often save something specifically because it is a bad argument or because it typifies a common bad argument that someone might make. I often save memes about beliefs and ideas that I disagree wiht. Do not take the presence of a meme here to mean I agree with or endorse the content, it may well be here specifically because I don't. This is my unfiltered stream of saving for any and all noteworthy memes.</p>
      
    <?php
      if(count($Pics)>1){
    ?>
      <div class="card-columns">
    <?php }else{ ?>
      </div>
    <?php } ?>
        <?php foreach($Pics as $Pic){ ?>
        <div class="card mb-4">
          <div class="card-body">
            <div class="card-text">
              <?php if(count($Pics)>1){ ?>
                <a href="?h=<?php echo $Pic['Hash']; ?>">
              <?php }else{ ?>
                <a href="<?php echo $Pic['URL']; ?>">
              <?php } ?>
                <img src="<?php echo $Pic['URL']; ?>" title="Saved <?php echo date('r',$Pic['Time']); ?>" width="300">
              </a>
              <small>Saved in <a href="?f=<?php echo $Pic['In']; ?>"><?php echo $Pic['In'].'</a><br>'.ago($Pic['Time']); ?> ago</small>
            </div><!--End Card-text-->
          </div><!--End Card-body-->
        </div><!--End Card-->
        <?php } ?>
      </div><!--/card-columns-->

    </div><!--/col-12-->

    <div class="col-12"><!--Begin Footer-->
      
      <h2>Pages</h2> 
      <?php
        // 
        // Jetzt mit Categories link f=
        //
        $Pages = count($Chunks);
        for ($i = 1; $i <= $Pages; $i++) {     
          $Page = $_GET['p']+1;
          if($i == $Page){
            echo '<a class="btn btn-small m-1 btn-info" href="?p='.$i.'&f='.$_GET['f'].'">'.$i.'</a> ';
          }else{
            echo '<a class="btn btn-small m-1 btn-outline-info" href="?p='.$i.'&f='.$_GET['f'].'">'.$i.'</a> ';
          }
        }
      ?>
      
      <h2>Categories</h2> 
      <p><?php
        global $Filter;
        sort($Tags);
        foreach($Tags as $Tag){
          if(
            ($_GET['f'] == $Tag)
          ){
            echo '<a class="m-1 btn btn-small btn-info" href="?f='.$Tag.'">'.$Tag.'</a>'."\n";
          }else{
            echo '<a class="m-1 btn btn-small btn-outline-info" href="?f='.$Tag.'">'.$Tag.'</a>'."\n";
          }
          
        }
      ?></p>
      
      <div class="text-muted text-center mt-1">
        
        <p>
          Check out my 
          <a href="//facebook.com/djcj88/" target="_blank">facebook</a>,
          <a href="//instagram.com/cjtrowbridge/" target="_blank">instagram</a>,
          <a href="//github.com/cjtrowbridge/" target="_blank">github</a>,
          <a href="//www.linkedin.com/in/cjtrowbridge" target="_blank">linkedin</a>, 
          <a href="//cjtrowbridge.com/resume/">resume</a>, 
          and <a href="//blog.cjtrowbridge.com" target="_blank">blog</a>.<br>
          Also feel free to check out my list of <a href="/maybe" target="_blank">back-burner projects</a>.
        </p>
      </div>
    </div><!--End Footer-->
    
  </div><!--End row-->
</div><!--End Container-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

<!-- 
Page--: <?php echo $_GET['p']."\n"; ?>
Filter: <?php echo $_GET['f']."\n"; ?>
Runtime: <?php echo round((microtime(true)-$Start),4)." seconds\n"; ?>
Ignored Extensions:
<?php
  foreach($IgnoredExtensions as $IgnoredExtension){
    if(!(
      $IgnoredExtension == 'md'||
      $IgnoredExtension == 'txt'||
      $IgnoredExtension == 'zip'||
      $IgnoredExtension == 'rar'
    )){
      echo $IgnoredExtension."\n";
    }
  }
?>
-->
    
</body>
</html>
