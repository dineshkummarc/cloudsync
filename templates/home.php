<div class="btn-group" id="books" style="float: left;">
    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="icon-list"></i> Cloud: <strong><?=$thiscloud["cloudname"]?></strong>
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
        <li><a data-toggle="modal" href="#newfield">New <strong>Cloud</strong></a></li>
        <li class="divider"></li>
        <?php foreach ($clouds as $key): ?>
            <li class="dropdown-submenu">
                <a tabindex="-1" href="cloud.php?snum=<?= $key["number"]?>"><?= $key["cloudname"] ?></a>
                <ul class="dropdown-menu">
                    <li><a onclick="$('#prinput').val('<?= $key["cloudname"]?>'); renamnum= <?=$key['number']?>;" data-toggle="modal" href="#prompt">Rename</a></li>
                    <li><a onclick="$('#edel').attr('href','cloud.php?dnum=<?= $key["number"]?>')" data-toggle="modal" href="#deletefield">Delete</a></li>
                </ul>
            </li>
        <?endforeach ?>
    </ul>
</div>

<span class="form-search" style="position: relative; margin-left:auto; margin-right:auto;">
<input class="input-medium search-query" autofocus id="search" name="search" placeholder="Search..." type="text" rel="tooltip" data-placement="bottom" title="Type to search, 'Esc' to clear"/>
</span>

<ul class="breadcrumb" style="margin-top: 7px;">
    Current Directory:
    <?php if($thiscloud["cloudnum"]==1){ // Webdav
        $url=$positions[0]["href"];
        $parse=parse_url($url);
        $array=explode("/",trim($parse["path"],"/"));
        echo "<li><a href='/'>Home</a>";
        $start=strlen($parse["scheme"]."://".$parse["host"].((isset($parse["port"]))?":".$parse["port"]."/":"/"))-1;
        for($i = 0; $i < count($array); $i++){
            if(!empty($array[$i])){
                echo" <span class='divider'> / </span> </li>";
                $start=$start+strlen($array[$i])+1;
                echo"<li><a href='/?url=".substr($url,0,$start)."'>".$array[$i]."</a>";
            }
        }echo"</li>";


    }elseif($thiscloud["cloudnum"]==2){ // Google Drive
        echo"<li><a href='/'>".$crumbs[0]["title"]."</a>";
        for($i = 1; $i < count($crumbs); $i++){
            echo"<span class='divider'> / </span> </li>";
            echo"<li><a href='/?id=".$crumbs[$i]["id"]."'>".$crumbs[$i]["title"]."</a>";
        }echo"</li>";
    }?>
</ul>

<div id="table1">
    <table class="table table-striped" id="table0">
        <thead class="head">
            <tr>
                <th>Type</th>
                <th>File/Folder Display Name<i rel="tooltip" class="icon-info-sign" title="Click header to sort"></i></th>
                <th>File size</th>
                <th>Last Modified</th>
            </tr>
        </thead>
        <tbody id="main">
        <?php if($thiscloud["cloudnum"]==1): // Webdav?>
            <?php foreach ($positions as $row): ?>
                <tr <?php if($row["href"]==$positions[0]["href"])echo"id='hide'";//if($row['folder']=="t")echo"class='fold'";else echo"class='file'";?>>
                    <td sorttable="<?=$row["folder"]?>"><?=($row['folder']=="t")?"<img src='img/folder.png'/>":"<img src='img/file.png'/>"?></td>
                    <td><div class="classes">
                            <a href="/?url=<?php echo$row['href'];if($row["folder"]=="f") echo"&file=t";?>"><?= $row['name']?></a>
                        </div></td>
                    <td sorttable=<?=(($row['size']!="f")&&!empty($row["size"]))?$row['size']:0;?>>
                        <?php if($row['size']!="f"){
                            if(empty($row["size"])){
                                echo 0;echo" B";
                            }
                            else{
                                $size=$row["size"];
                                $j=0;
                                while($size>1024){
                                    $size=$size/1024;
                                    $j++;
                                }
                                $unit=array('B','KB','MB','GB','TB');
                                echo round($size,1)." ".$unit[$j];
                            }
                        }?>
                    </td>
                    <td sorttable=<?=($row["folder"]=="f")?$row["modified"]:1;?>>
                        <?php if($row["folder"]=="f")echo date('D, d M Y H:i:s',$row['modified']);else echo"---"?>
                    </td>
                </tr>
            <? endforeach ?>

        <?elseif($thiscloud["cloudnum"]==2): // Google Drive?>
            <?php foreach ($positions as $row): ?>
                <tr <?php //if($row['folder']=="t")echo"class='fold'";else echo"class='file'";?>>
                    <td sorttable="<?=$row["folder"]?>"><img src='<?=$row["icon"]?>'/></td>
                    <td><div class="classes">
                            <a href="/?id=<?php echo$row['id'];if($row["folder"]=="f") echo"&file=t";?>"><?= $row['name']?></a>
                        </div></td>
                    <td sorttable=<?=(($row['size']!="f")&&!empty($row["size"]))?$row['size']:0;?>>
                        <?php if($row['size']!="f"){
                            $size=$row["size"];
                            $j=0;
                            while($size>1024){
                                $size=$size/1024;
                                $j++;
                            }
                            $unit=array('B','KB','MB','GB','TB');
                            echo round($size,1)." ".$unit[$j];
                        }?>
                    </td>
                    <td sorttable=<?=$row["modified"];?>>
                        <?php echo date('D, d M Y H:i:s',$row['modified']);?>
                    </td>
                </tr>
            <? endforeach ?>
        <?endif?>
        </tbody>
    </table>
</div>

<!--   MODALS   -->
<div id="deletefield" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="$('#edel').attr('href','#')">&times;</button>
        <h3>Delete Connector?</h3>
    </div>
    <div class="modal-body"><p>Are you sure you want to delete this connector?</p></div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true" onclick="$('#edel').attr('href','#')">Cancel</a>
        <a class="btn btn-danger" id="edel" href="">Delete</a>
    </div>
</div>

<div id="prompt" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <a href="#" class="close" data-dismiss="modal" onclick="$('#prinput').val(''); renamnum=null">&times;</a>
        <h3>Enter name of Connector</h3>
    </div>
    <div class="modal-body">
        <input type="text" value="" id="prinput"/>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true" onclick="$('#prinput').val(''); renamnum=null">Cancel</a>
        <a id="edial" class="btn btn-primary" onclick="window.location.href='cloud.php?rnum='+renamnum+'&nam='+$('#prinput').val()">OK</a>
    </div>
</div>

<div id="newfield" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <a href="#" class="close" data-dismiss="modal" onclick="$('#prinput').val('')">&times;</a>
        <h3>New Connector</h3>
    </div>
    <form id="daform" method="post" action="cloud.php">
        <div class="modal-body">
            <input type="text" placeholder="Name of Connector" name="name"/><br>
            <select name="provider" id="prov" onchange="chkprov()">
                <option value=1>WebDAV</option>
                <option value=2>Google Drive</option>
            </select>
            <div id="davy">
                <input type="text" placeholder="DAV Url(http://abc.com:80/)" name="url"/><br>
                <span style="color: dimgrey">Eg:- http[s]://dav.abc.com:[80|443]/</span><br>
                <input type="text" placeholder="Username/Email" name="username"/><br>
                <input type="password" placeholder="Password" name="password"/>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal" aria-hidden="true" onclick="$('#prinput').val('')">Cancel</a>
            <a class="btn btn-primary" onclick="$('#daform').submit()">OK</a>
        </div>
    </form>
</div>

<script type="text/javascript">//////////////////////////////////////////    SCRIPTS     ////////////////////////////////////////////////
    // Search Box Functions
    $(document).ready(function() {
        $("#search").keyup(function(e) {
            //$('.classes').unhighlight();
            if (e.keyCode == 27) {// clear search field on "Esc" key
                document.getElementById("search").value = "";
            }

            // Instant Search Functionality
            var val = $(this).val().toLowerCase().split(" ");
            var valtrim = trim($(this).val().toLowerCase());
            $("#table0 tbody tr").hide();
            $("#table0 tbody tr").each(function() {//for each row (tr)
                var text = $(this).find(".classes").text().toLowerCase();
                for (var i = 0; i < val.length; i++) {//for each word in search field
                    if ((text.indexOf(val[i]) != -1 && val[i] != "") || valtrim == "") {//if match found OR search box empty
                        $(this).show();
                    }
                }
            });
            // HIGHLIGHT (http://bartaz.github.io/sandbox.js/jquery.highlight.html)
            //$('.classes').highlight(val);
        });
    });


    //trims white space from string (search function)
    function trim (str) {
        return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    }

    // Initialize Tooltip and sortable
    $("[rel='tooltip']").tooltip();

    function chkprov(){ // New form show/hide dav element
        var prov = document.getElementById("prov");
        var selectedValue = prov.options[prov.selectedIndex].value;
        if (selectedValue == 1){
            $("#davy").show();
        }else if(selectedValue == 2){
            $("#davy").hide();
        }
    }
</script>