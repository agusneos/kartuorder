<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/webcamjs/webcam.js')?>"></script>
<script type="text/javascript">
    $.extend($.fn.datebox.defaults,{
        formatter:function(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        },
        parser:function(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
    });
        
    $.extend($.fn.datetimebox.defaults,{
        formatter:function(date){
            var h = date.getHours();
            var M = date.getMinutes();
            var s = date.getSeconds();
            function formatNumber(value){
                return (value < 10 ? '0' : '') + value;
            }
            var separator = $(this).datetimebox('spinner').timespinner('options').separator;
            var r = $.fn.datebox.defaults.formatter(date) + ' ' + formatNumber(h)+separator+formatNumber(M);
            if ($(this).datetimebox('options').showSeconds){
                r += separator+formatNumber(s);
            }
            return r;
        },
        parser:function(s){
            if ($.trim(s) == ''){
                return new Date();
            }
            var dt = s.split(' ');
            var d = $.fn.datebox.defaults.parser(dt[0]);
            if (dt.length < 2){
                return d;
            }
            var separator = $(this).datetimebox('spinner').timespinner('options').separator;
            var tt = dt[1].split(separator);
            var hour = parseInt(tt[0], 10) || 0;
            var minute = parseInt(tt[1], 10) || 0;
            var second = parseInt(tt[2], 10) || 0;
            return new Date(d.getFullYear(), d.getMonth(), d.getDate(), hour, minute, second);
        }
    });
</script>
<!-- Data Grid -->
<table id="grid-transaksi_bor"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_bor">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'bor_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'bor_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'bor_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'bor_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Bor</th>
            <th data-options="field:'bor_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_bor = [{
        text:'New',
        id:'baruBor',
        iconCls:'icon-new_file',
        handler:function(){transaksiBorCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiBorUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiBorHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiBorSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiBorImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_bor').datagrid('reload');}
    },{
        text:'Update Tgl. Bor After',
        iconCls:'icon-date',
        handler:function(){transaksiBorAfter();}
    },{
        text:'Update Tgl. Bor Between',
        iconCls:'icon-date',
        handler:function(){transaksiBorBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiBorCheck();}
    }];
    
    $('#grid-transaksi_bor').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/bor/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruBor').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiBorCreate() {
        $('#dlg-transaksi_bor').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_bor').form('clear');
        //$('#bor_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/bor/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/bor/getDateBor'); ?>',function(result){
            $('#bor_date').datebox('setValue', result.sesdate);
            },'json'); //ambil session date

        Webcam.set({
            width: 430,
            height: 240,
            dest_width: 1280,
            dest_height: 720,
            force_flash: true,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        Webcam.setSWFLocation('assets/webcamjs/webcam.swf');
        Webcam.attach('#webcamBor');
        
        $('#captureBor').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureBor').focus();
        $('#captureBor').bind('click', function(){
            $('#captureBor').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#bor_lot').textbox('setValue', '');
            $('#bor_customer').textbox('setValue', '');
            $('#bor_barang').textbox('setValue', '');
            $('#bor_sub').textbox('setValue', '');
            $('#bor_lot').next().find('input').focus();
        });
       
        $('#bor_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotBor').focus();
            }
        });
        
        $('#chlotBor').bind('click', function(){
            $('#bor_sub').next().find('input').focus();
        });
        
        $('#bor_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#bor_oksave').focus();
            }
        });
    }
    
    function captureBor() {
        Webcam.snap( function(data_uri) {
            $('#imgBor').textbox('setValue',data_uri);
        });
    }
    
    function checkLotBor() {
        var lotidBor = $('#bor_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/bor/cekLot'); ?>',{bor_lot:lotidBor},function(result){
            if (result.success){
                $('#bor_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/bor/getCustItem'); ?>',{bor_lot:lotidBor},function(result){
                    $('#bor_customer').textbox('setValue', result.customer);
                    $('#bor_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#bor_lot').textbox('setValue', '');
                $('#bor_customer').textbox('setValue', '');
                $('#bor_barang').textbox('setValue', '');
                $('#bor_sub').textbox('setValue', '');
                $('#bor_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiBorUpdate() {
        var row = $('#grid-transaksi_bor').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_bor-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_bor-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/bor/update'); ?>/' + row.bor_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiBorSave(){
        $('#fm-transaksi_bor').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_bor').dialog('close');
                    //$('#grid-transaksi_bor').datagrid('reload');
                    transaksiBorCreate();
                    $.messager.show({
                        title: 'Info',
                        msg: 'Input Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Input Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiBorSaveEdit(){
        $('#fm-transaksi_bor-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_bor-edit').dialog('close');
                    $('#grid-transaksi_bor').datagrid('reload');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiBorHapus(){
        var row = $('#grid-transaksi_bor').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Bor '+row.bor_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/bor/delete'); ?>',{bor_id:row.bor_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_bor').datagrid('reload');
                            $.messager.show({
                                title: 'Info',
                                msg: 'Hapus Data Berhasil'
                            });
                        } else {
                            $.messager.show({
                                title: 'Error',
                                msg: 'Hapus Data Gagal'
                            });
                        }
                    },'json');
                }
            });
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiBorImage(){
        var row = $('#grid-transaksi_bor').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/bor/viewImage'); ?>',{bor_id:row.bor_id},function(result){
                if (result.success){
                    var content = '<iframe scbor="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.bor_id;
                    if ($('#tt').tabs('exists', title))
                    {
                        $('#tt').tabs('select', title);
                        $('#dlg').dialog('close');
                    } 
                    else 
                    {
                        $('#tt').tabs('add',{
                            title:title,
                            content:content,
                            closable:true,
                            iconCls:'icon-picture'
                        });
                        $('#dlg').dialog('close');
                    }
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Gambar Tidak Ditemukan'
                    });
                }
            },'json');
            
        }
        else
        {
            $.messager.alert('Info','Data belum dipilih !','info');
        }
    }

    function transaksiBorSesdate()
    {
        $('#dlg-transaksi_bor_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_bor_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/bor/getDateBor'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiBorSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/bor/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_bor_sesdate').dialog('close');
                $.messager.show({
                    title: 'Info',
                    msg: 'Ubah Data Berhasil'
                });
            }
            else
            {
                $.messager.show({
                    title: 'Error',
                    msg: 'Ubah Data Gagal'
                });
            }
        },'json');
    }
    
    function transaksiBorAfter()
    {
        $('#dlg-transaksi_bor-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Bor After');
        $('#fm-transaksi_bor-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/bor/updateAfter'); ?>';
    }
    
    function transaksiBorAfterSave()
    {
        $('#fm-transaksi_bor-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_bor').datagrid('reload');
                    $('#dlg-transaksi_bor-after').dialog('close');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiBorBetween()
    {
        $('#dlg-transaksi_bor-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Bor Between');
        $('#fm-transaksi_bor-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/bor/updateBetween'); ?>';
    }
    
    function transaksiBorBetweenSave()
    {
        $('#fm-transaksi_bor-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_bor').datagrid('reload');
                    $('#dlg-transaksi_bor-between').dialog('close');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiBorCheck()
    {
        $('#dlg-transaksi_bor-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_bor-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/bor/check'); ?>';
    }
    
    function transaksiBorCheckSave()
    {
        $('#fm-transaksi_bor-check').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success)
                {
                    $.messager.show({
                        title: 'Info',
                        msg: 'Total Entry Data Tanggal '+result.tgl+' Sebanyak '+result.total+' Data'
                    }); 
                }                               
            }
        });
    }
    
</script>
<style type="text/css">
    #fm-transaksi_bor{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_bor-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_bor-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_bor-between{
        margin:0;
        padding:10px 30px;
    }
    .ftitle{
        font-size:14px;
        font-weight:bold;
        padding:5px 0;
        margin-bottom:10px;
        border-bottom:1px solid #ccc;
    }
    .fitem{
        margin-bottom:5px;
    }
    .fitem label{
        display:inline-block;
        width:100px;
    }
    .fitem input{
        display:inline-block;
        width:150px;
    }
    .center a{
        margin-left: 200px;
        margin-top: 5px;
        margin-bottom: 10px;
    }
    .grup2{
        margin-left: 75px;
    }

</style>

<div id="dlg-transaksi_bor" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_bor">
    <form id="fm-transaksi_bor" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamBor">
            </div>
        </div>
        <div class="center">
            <a id="captureBor" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureBor()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgBor" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="bor_lot" name="bor_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotBor" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotBor()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="bor_sub" name="bor_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="bor_customer" name="bor_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="bor_barang" name="bor_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Bor</label>
                <input id="bor_date" name="bor_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_bor">
    <a href="javascript:void(0)" id="bor_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiBorSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_bor').dialog('close');Webcam.reset();$('#grid-transaksi_bor').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_bor-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_bor-edit">
    <form id="fm-transaksi_bor-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="bor_lot" name="bor_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="bor_sub" name="bor_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Bor</label>
            <input type="text" id="bor_date" name="bor_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_bor-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBorSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_bor-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_bor_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_bor_sesdate">
    <form id="fm-transaksi_bor_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_bor_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBorSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_bor_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_bor-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_bor-after">
    <form id="fm-transaksi_bor-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Bor</label>
            <input type="text" id="aa" name="aa" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Setelah Input</label>
            <input type="text" id="bb" name="bb" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Update Ke Tanggal</label>
            <input type="text" id="cc" name="cc" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_bor-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBorAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_bor-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_bor-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_bor-between">
    <form id="fm-transaksi_bor-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Bor</label>
            <input type="text" id="dd" name="dd" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Setelah Input</label>
            <input type="text" id="ee" name="ee" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sebelum Input</label>
            <input type="text" id="ff" name="ff" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Update Ke Tanggal</label>
            <input type="text" id="gg" name="gg" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_bor-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBorBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_bor-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_bor-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_bor-check">
    <form id="fm-transaksi_bor-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Bor</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_bor-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiBorCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_bor-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_bor.php -->
<!-- Location: ./application/views/transaksi/v_bor.php -->