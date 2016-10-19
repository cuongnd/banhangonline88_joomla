/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 17/10/2016
 * Time: 9:15 CH
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Net;
using System.Reflection;
using System.Text;
using System.Windows.Forms;
using monitorscreenshot.includes;
using monitorscreenshot.libraries;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
namespace monitorscreenshot
{
	/// <summary>
	/// Description of setting.
	/// </summary>
	public class setting
	{
		public setting()
		{
			
		}

		public static void save_user_info(String username,String password)
		{
			var user_item=user.getInstance();
			user_item.username=username;
			user_item.password=password;
			JToken token = JObject.FromObject(user_item);
			String str_json_user_info=token.ToString();
			
			String m_exePath = Path.GetDirectoryName(Assembly.GetExecutingAssembly().Location);
			String  setting_file_path= m_exePath + "\\" + "setting.txt";

			if(!File.Exists(setting_file_path))
			{
				File.Create(setting_file_path);
			}
			File.WriteAllText(setting_file_path,str_json_user_info);
		}

		public static String get_content_setting_from_file_setting()
		{
			String m_exePath = Path.GetDirectoryName(Assembly.GetExecutingAssembly().Location);
			String  setting_file_path= m_exePath + "\\" + "setting.txt";
			Boolean is_new_file=false;
			if(!File.Exists(setting_file_path))
			{
				File.Create(setting_file_path);
				is_new_file=true;
				
			}
			String file_content="";
			if(is_new_file)
			{
				file_content="";
			}else{
				file_content=File.ReadAllText(setting_file_path);
			}
			return file_content;
		}
	
		public static void load_setting()
		{
			String file_content=get_content_setting_from_file_setting();
			
			JToken json_user = JObject.Parse(file_content);
			

			file_content=utility.Base64Encode(file_content);
			var request = (HttpWebRequest)WebRequest.Create(definesconst.ROOT_URL+"index.php?option=com_quanlynhanvien&task=user.ajax_remote_login");

			var postData = "file_content="+file_content;

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
			JToken json_responseString = JObject.Parse(responseString);
			
			var  e = (int)json_responseString.SelectToken("e");
			var  m = (String)json_responseString.SelectToken("m");
			if(e==1)
			{
				login form_login = new login(); 
		        form_login.Show(); 
			}else if(e==0){
				var user_item=user.getInstance();
				user_item.id=(int)json_responseString.SelectToken("user.id");
		
				user_item.username = (String)json_user.SelectToken("username");
				user_item.password = (String)json_user.SelectToken("password");
			
				//user_item.username=json_user;
			}
  		}
	}
}
