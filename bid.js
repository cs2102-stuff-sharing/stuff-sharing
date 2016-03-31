function addBid(minbid, userpoint) {
    var bid = prompt("How many points do you want to bid?\n" + "Minimum Bid: " + minbid + "\nYour Point: " + userpoint);
    
		if (bid == null)
		{
			return;
		}
    if (parseInt(bid) >= parseInt(minbid) && parseInt(bid) <= parseInt(userpoint)) {
        document.getElementById('bidpoint').value = bid;
				document.getElementById('bidform').submit();
    }
		else
		{
			return addBid(minbid, userpoint);
		}
}

function update(minbid, userpoint, currentbid) {
		var usablepoint = parseInt(userpoint) + parseInt(currentbid);
    var bid = prompt("Change the bid point to? (enter 0 to retract)\n" + "Minimum Bid: " + minbid + "\nYour Usable Point: " + usablepoint.toString());
		if (bid == null)
		{
			return;
		}

		if (parseInt(bid) == 0 || (parseInt(bid) >= parseInt(minbid) && parseInt(bid) <= usablepoint)) {
        document.getElementById('updatepoint').value = bid;
				var recoverpoint = usablepoint - parseInt(bid);
				document.getElementById('recoverpoint').value = recoverpoint.toString();
				document.getElementById('updateform').submit();
		}
		else
		{
			return update(minbid, userpoint, currentbid);
		}
}