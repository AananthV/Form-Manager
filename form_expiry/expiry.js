let mysql = require('mysql');
let config = require('./config.js');

let con = mysql.createConnection({
  host: config.DB_HOST,
  user: config.DB_USERNAME,
  password: config.DB_PASSWORD,
  database: config.DB_NAME
});
console.log("success");
setInterval(function() {
  let sql = "UPDATE forms SET active = 0, expires = 0 WHERE (expires = 1 AND CURRENT_TIMESTAMP() > expiry)";
  con.query(sql, function (err, result) {
    if (err) throw err;
    console.log(result.affectedRows + " record(s) updated");
  });
}, 60000);
