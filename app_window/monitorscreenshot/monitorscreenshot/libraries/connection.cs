/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 18/10/2016
 * Time: 8:25 CH
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Data;
using Finisar.SQLite;

namespace monitorscreenshot.libraries
{
	/// <summary>
	/// Description of connection.
	/// </summary>
	public class connection
	{
		private SQLiteConnection sql_con;
		private SQLiteCommand sql_cmd;
		private SQLiteDataAdapter DB;
		private DataSet DS = new DataSet();
		private DataTable DT = new DataTable();
		private volatile static connection uniqueInstance;
		public connection()
		{
			sql_con = new SQLiteConnection("Data Source=H:\\project\\banhangonline88_joomla\\app_window\\monitorscreenshot\\monitorscreenshot\\monitorcsreen.db;Version=3;New=False;Compress=True;");
			sql_con.Open();			
		}
		public int ExecuteQuery(String txtQuery) 
		{ 
			
			sql_cmd = sql_con.CreateCommand(); 
			sql_cmd.CommandText=txtQuery; 
			int rowsAffected=sql_cmd.ExecuteNonQuery(); 
			
			return rowsAffected;

		}
		public DataTable LoadData(String table_name) 
		{ 


			sql_cmd = sql_con.CreateCommand(); 
			String CommandText = "select * from "+table_name; 
			DB = new SQLiteDataAdapter(CommandText,sql_con); 
			DS.Reset(); 
			DB.Fill(DS); 
			DT= DS.Tables[0]; 
			return DT;

		}
		public DataTable LoadDataByQuery(String query) 
		{ 


			sql_cmd = sql_con.CreateCommand(); 

			DB = new SQLiteDataAdapter(query,sql_con); 
			DS.Reset(); 
			DB.Fill(DS); 
			DT= DS.Tables[0]; 
			return DT;

		}
		public static connection getInstance()
		{
			if (uniqueInstance == null)
	        {
				uniqueInstance = new connection();
	        }
	        return uniqueInstance;
        
		}
		
	}
}
