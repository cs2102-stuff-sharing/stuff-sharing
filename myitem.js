function askMinBid(pointid, formid) {
    var minbid = prompt("Please enter the minimum bid point");
    
    if (parseInt(minbid) > 0) {
        document.getElementById(pointid).value = minbid;
				document.getElementById(formid).submit();
    }
}
