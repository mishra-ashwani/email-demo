window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        new simpleDatatables.DataTable(datatablesSimple);
    }


    const tbl = document.getElementById('tblSmtpList');
    if(tbl){
        new simpleDatatables.DataTable("#tblSmtpList");
    
    }
    const emailLogs = document.getElementById('emailLogs');
    if(emailLogs){
        new simpleDatatables.DataTable("#emailLogs",{
            perPage:100,
            perPageSelect:[15, 20, 25]
        });
    
    }
});

