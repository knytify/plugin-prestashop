function pad(n, width, z) {
    z = z || "0";
    n = n + "";
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
  }

  const formatDate = (date_obj) => {
    return (
      date_obj.getFullYear() +
      "-" +
      pad(date_obj.getMonth() + 1, 2) +
      "-" +
      pad(date_obj.getDate(), 2)
    );
  };

  const formatDateForDisplay = (date_obj) => {
    return (
      pad(date_obj.getDate(), 2) +
      "/" +
      pad(date_obj.getMonth() + 1, 2) +
      "/" +
      date_obj.getFullYear()
    );
  };

  const formatForChartFromStr = (date_str) => {
    const parts = date_str.split("-")
    return parts[2] + "/" + parts[1]
  }

  export { formatDate, formatDateForDisplay, formatForChartFromStr };
