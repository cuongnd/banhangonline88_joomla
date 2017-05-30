/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 18/10/2016
 * Time: 10:52 SA
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Net;
using System.Text;
using System.Windows.Forms;
using monitorscreenshot.includes;
using Newtonsoft.Json.Linq;

namespace monitorscreenshot.libraries
{
	/// <summary>
	/// Description of user.
	/// </summary>
	public class user
	{
		public int id=0;
		public String username="";
		public String password="";
		public user()
		{
		}
		private volatile static user uniqueInstance;
		public static user getInstance()
		{
			if (uniqueInstance == null)
	        {
				uniqueInstance = new user();
	        }
	        return uniqueInstance;
        
		}
		
		
		public static Boolean ajax_remote_check_exists_user(String username,String password)
		{
			var request = (HttpWebRequest)WebRequest.Create(definesconst.ROOT_URL+"index.php?option=com_quanlynhanvien&task=user.ajax_remote_check_exists_user");

			var postData = "username="+username;
			postData+= "&password="+password;
			var data = Encoding.ASCII.GetBytes(postData);
			
			request.Method = "POST";
			request.ContentType = "application/x-www-form-urlencoded";
			request.ContentLength = data.Length;
			
			using (var stream = request.GetRequestStream())
			{
			    stream.Write(data, 0, data.Length);
			}
			
			var response = (HttpWebResponse)request.GetResponse();
			
			var responseString = new StreamReader(response.GetResponseStream()).ReadToEnd();
			
			
			JToken token = JObject.Parse(responseString);

			int e = (int)token.SelectToken("e");
			String m = (String)token.SelectToken("m");
			if(e==1)
			{
				MessageBox.Show(m);
				return false;
			}else if(e==0){
				MessageBox.Show(m);
				return true;
			}
			return false;
		}
	}
}
