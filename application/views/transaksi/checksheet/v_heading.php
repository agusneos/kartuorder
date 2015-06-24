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
<table id="grid-transaksi_heading"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_heading">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'heading_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'heading_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'heading_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'heading_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Heading</th>
            <th data-options="field:'heading_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_heading = [{
        text:'New',
        id:'baruHeading',
        iconCls:'icon-new_file',
        handler:function(){transaksiHeadingCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiHeadingUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiHeadingHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiHeadingSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiHeadingImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_heading').datagrid('reload');}
    },{
        text:'Update Tgl. Heading After',
        iconCls:'icon-date',
        handler:function(){transaksiHeadingAfter();}
    },{
        text:'Update Tgl. Heading Between',
        iconCls:'icon-date',
        handler:function(){transaksiHeadingBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiHeadingCheck();}
    }];
    
    $('#grid-transaksi_heading').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/heading/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruHeading').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiHeadingCreate() {
        $('#dlg-transaksi_heading').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_heading').form('clear');
        //$('#heading_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/heading/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/heading/getDateHeading'); ?>',function(result){
            $('#heading_date').datebox('setValue', result.sesdate);
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
        Webcam.attach('#webcamHeading');
        
        $('#captureHeading').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureHeading').focus();
        $('#captureHeading').bind('click', function(){
            $('#captureHeading').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#heading_lot').textbox('setValue', '');
            $('#heading_customer').textbox('setValue', '');
            $('#heading_barang').textbox('setValue', '');
            $('#heading_sub').textbox('setValue', '');
            $('#heading_lot').next().find('input').focus();
        });
       
        $('#heading_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotHeading').focus();
            }
        });
        
        $('#chlotHeading').bind('click', function(){
            $('#heading_sub').next().find('input').focus();
        });
        
        $('#heading_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#heading_oksave').focus();
            }
        });
    }
    
    function captureHeading() {
        Webcam.snap( function(data_uri) {
            $('#imgHeading').textbox('setValue',data_uri);
        });
    }
    
    function checkLotHeading() {
        var lotidHeading = $('#heading_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/heading/cekLot'); ?>',{heading_lot:lotidHeading},function(result){
            if (result.success){
                $('#heading_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/heading/getCustItem'); ?>',{heading_lot:lotidHeading},function(result){
                    $('#heading_customer').textbox('setValue', result.customer);
                    $('#heading_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#heading_lot').textbox('setValue', '');
                $('#heading_customer').textbox('setValue', '');
                $('#heading_barang').textbox('setValue', '');
                $('#heading_sub').textbox('setValue', '');
                $('#heading_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiHeadingUpdate() {
        var row = $('#grid-transaksi_heading').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_heading-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_heading-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/heading/update'); ?>/' + row.heading_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiHeadingSave(){
        $('#fm-transaksi_heading').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_heading').dialog('close');
                    //$('#grid-transaksi_heading').datagrid('reload');
                    transaksiHeadingCreate();
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
    
    function transaksiHeadingSaveEdit(){
        $('#fm-transaksi_heading-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_heading-edit').dialog('close');
                    $('#grid-transaksi_heading').datagrid('reload');
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
    
    function transaksiHeadingHapus(){
        var row = $('#grid-transaksi_heading').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Heading '+row.heading_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/heading/delete'); ?>',{heading_id:row.heading_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_heading').datagrid('reload');
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
    
    function transaksiHeadingImage(){
        var row = $('#grid-transaksi_heading').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/heading/viewImage'); ?>',{heading_id:row.heading_id},function(result){
                if (result.success){
                    var content = '<iframe scheading="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.heading_id;
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

    function transaksiHeadingSesdate()
    {
        $('#dlg-transaksi_heading_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_heading_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/heading/getDateHeading'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiHeadingSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/heading/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_heading_sesdate').dialog('close');
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
    
    function transaksiHeadingAfter()
    {
        $('#dlg-transaksi_heading-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Heading After');
        $('#fm-transaksi_heading-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/heading/updateAfter'); ?>';
    }
    
    function transaksiHeadingAfterSave()
    {
        $('#fm-transaksi_heading-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_heading').datagrid('reload');
                    $('#dlg-transaksi_heading-after').dialog('close');
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
    
    function transaksiHeadingBetween()
    {
        $('#dlg-transaksi_heading-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Heading Between');
        $('#fm-transaksi_heading-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/heading/updateBetween'); ?>';
    }
    
    function transaksiHeadingBetweenSave()
    {
        $('#fm-transaksi_heading-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_heading').datagrid('reload');
                    $('#dlg-transaksi_heading-between').dialog('close');
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
    
    function transaksiHeadingCheck()
    {
        $('#dlg-transaksi_heading-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_heading-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/heading/check'); ?>';
    }
    
    function transaksiHeadingCheckSave()
    {
        $('#fm-transaksi_heading-check').form('submit',{
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
    #fm-transaksi_heading{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_heading-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_heading-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_heading-between{
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

<div id="dlg-transaksi_heading" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_heading">
    <form id="fm-transaksi_heading" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamHeading">
            </div>
        </div>
        <div class="center">
            <a id="captureHeading" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureHeading()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgHeading" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="heading_lot" name="heading_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotHeading" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotHeading()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="heading_sub" name="heading_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="heading_customer" name="heading_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="heading_barang" name="heading_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Heading</label>
                <input id="heading_date" name="heading_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_heading">
    <a href="javascript:void(0)" id="heading_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiHeadingSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_heading').dialog('close');Webcam.reset();$('#grid-transaksi_heading').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_heading-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_heading-edit">
    <form id="fm-transaksi_heading-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="heading_lot" name="heading_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="heading_sub" name="heading_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Heading</label>
            <input type="text" id="heading_date" name="heading_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_heading-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiHeadingSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_heading-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_heading_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_heading_sesdate">
    <form id="fm-transaksi_heading_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_heading_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiHeadingSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_heading_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_heading-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_heading-after">
    <form id="fm-transaksi_heading-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Heading</label>
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
<div id="dlg-buttons-transaksi_heading-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiHeadingAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_heading-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_heading-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_heading-between">
    <form id="fm-transaksi_heading-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Heading</label>
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
<div id="dlg-buttons-transaksi_heading-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiHeadingBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_heading-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_heading-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_heading-check">
    <form id="fm-transaksi_heading-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Heading</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_heading-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiHeadingCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_heading-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_heading.php -->
<!-- Location: ./application/views/transaksi/v_heading.php -->