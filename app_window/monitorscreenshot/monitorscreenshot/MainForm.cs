/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 16/10/2016
 * Time: 11:08 SA
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Collections.Generic;
using System.Data;
using System.Drawing;
using System.Drawing.Imaging;
using System.IO;
using System.Net;
using System.Text;
using System.Threading;
using System.Windows.Forms;
using System.Net.Http;
using monitorscreenshot.includes;
using monitorscreenshot.libraries;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
namespace monitorscreenshot
{
	/// <summary>
	/// Description of MainForm.
	/// </summary>
	public partial class MainForm : Form
	{
		Boolean state_capture=false;
		public String prev_str_now="";
		public ftp ftpClient;
		public int total_capture=1;
		private NotifyIcon trayIcon;
		public MainForm()
		{
			//
			// The InitializeComponent() call is required for Windows Forms designer support.
			//
			InitializeComponent();

			
			//
			// TODO: Add constructor code after the InitializeComponent() call.
			//
		}
		void btn_playClick(object sender, EventArgs e)
		{
			ftpClient = new ftp(@"ftp://103.45.230.226/", "banhangonl", "IMhySiU0V");
			if(Properties.Settings1.Default.user=="")
			{
				MessageBox.Show(Properties.Settings1.Default.user);
			}
			//Properties.Settings1.Default.user="sdfsdfdsfdsfd";
			
			Properties.Settings1.Default.user="sdfdsfdsfdsfdsfdsfd";
			Properties.Settings1.Default.Save();
			state_capture=!state_capture;
			start_capture.Enabled=state_capture;
			timer_synchronous.Enabled=state_capture;
			if(state_capture)
			{
				btn_play.Image= (Image)Properties.Resource1.pause;
			}
			else{
				btn_play.Image= (Image)Properties.Resource1.play1;
			}
			if(state_capture)
			{
				
				
			}

// Create a graphics object from the bitmap.

// Take the screenshot from the upper left corner to the right bottom corner.

// Save the screenshot to the specified path that the user has chosen.

	
		}

		void capture_WorkThread()
		{
			total_capture++;
			DateTime the_now = DateTime.Now;
     		String the_now1= the_now.ToString("yyyy-MM-dd H:mm:ss");
     
     
			String str_now= the_now.ToString("H_mm_ss_dd_MM_yyyy");
			if(prev_str_now=="")
			{
				prev_str_now=str_now;
			}
			String file_capture_name=prev_str_now+"_"+str_now+".png";
			prev_str_now=str_now;

				//Create a new bitmap.
			String screenshotpng_path = moniter_config.screenshotpng_path()+file_capture_name; // @"C:\Users\cuongnd\Desktop\Screenshot.png";
			var bmpScreenshot = new Bitmap(Screen.PrimaryScreen.Bounds.Width, Screen.PrimaryScreen.Bounds.Height, PixelFormat.Format32bppArgb);
			var gfxScreenshot = Graphics.FromImage(bmpScreenshot);
			gfxScreenshot.CopyFromScreen(Screen.PrimaryScreen.Bounds.X, Screen.PrimaryScreen.Bounds.Y, 0, 0, Screen.PrimaryScreen.Bounds.Size, CopyPixelOperation.SourceCopy);
			bmpScreenshot=utility.GrayScale(bmpScreenshot);
			try{
				bmpScreenshot.Save(screenshotpng_path, ImageFormat.Png);
			}catch(Exception ex)
			{
				LogWriter.LogWrite(ex.ToString());
			}
			
			var connection_item=connection.getInstance();
			
			var user_item=user.getInstance();
			int user_id=user_item.id;
			String txtSQLQuery = "insert into  capturescreen (create_on,user_id,filename,synchronoused,uploaded) values ('"+the_now1+"','"+user_id+"','"+file_capture_name+"','0','0')";
			try{
				int rowsAffected=connection_item.ExecuteQuery(txtSQLQuery);     	
			}catch(Exception ex)
			{
				LogWriter.LogWrite(txtSQLQuery);
				LogWriter.LogWrite(ex.ToString());
				state_capture=false;
				start_capture.Enabled=state_capture;

				timer_synchronous.Enabled=state_capture;
				
			
				
				
			}
		}

		void Start_captureTick(object sender, EventArgs e)
		{
			Thread th = new Thread(new ThreadStart(capture_WorkThread));
	        th.Start();
	        
	        
			
					

		}
		void MainFormLoad(object sender, EventArgs e)
		{
			 Thread th = new Thread(new ThreadStart(init_WorkThread));
	        th.Start();
		}
		public void init_WorkThread() {
            index.init();
            hide_panel();
            
            
		}
		private void hide_panel()
		{
			 if (InvokeRequired)
			 {
			 	 MethodInvoker method = new MethodInvoker(hide_panel);
			 	 Invoke(method);
			 	 return;
			 }
            var user_item=user.getInstance();
            
			if(user_item.id!=0)
			{
				this.panel_loading.Hide();
				pictureBox_loading.Hide();
				lab_name.Text=user_item.username;
				

			}
		}
		void Exit(object sender, EventArgs e)
	    {
	    }

		void synchronous_WorkThread()
		{
			var connection_item=connection.getInstance();
			DataTable list_screen=new DataTable();
			String query="";
			try{
		    	list_screen=connection_item.LoadDataByQuery("select * from  capturescreen WHERE synchronoused=0");
		    }catch(Exception ex){
		    	LogWriter.LogWrite(ex.ToString());
		    }
			// On all tables' rows
			if(list_screen.Rows.Count>0)
			{
				String[] list_json_screen = new String[list_screen.Rows.Count];
				List<screens> list_item_screen = new List<screens>();

				int i=0;
				
				foreach(DataRow row in list_screen.Rows)
				 { 
					var screen_item=screens.getInstance();
	
					screen_item.id=Int32.Parse(row["id"].ToString());
					screen_item.create_on=(String)row["create_on"];
					screen_item.file_name=(String)row["filename"];
					String synchronoused=row["synchronoused"].ToString();
					screen_item.synchronoused=Int32.Parse(row["synchronoused"].ToString());
					screen_item.uploaded=Int32.Parse(row["uploaded"].ToString());
					list_item_screen.Add(screen_item);
					screen_item.user_id=Int32.Parse(row["user_id"].ToString());
				     JToken json_row = JObject.FromObject(screen_item);
				     list_json_screen[i]=json_row.ToString();
				     i++;
				 }
				 JToken json_list_json_screen = JsonConvert.SerializeObject(list_json_screen);
				     
				 json_list_json_screen=utility.Base64Encode(json_list_json_screen.ToString());
				var request = (HttpWebRequest)WebRequest.Create(definesconst.ROOT_URL+"index.php?option=com_quanlynhanvien&task=screen.ajax_save_remote_screen");
	
				var postData = "json_list_json_screen="+json_list_json_screen;
	
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
				
				int  int_e = (int)json_responseString.SelectToken("e");
				var  m = (String)json_responseString.SelectToken("m");
				if(int_e==1)
				{
					MessageBox.Show(m);
				}else if(int_e==0){
					list_item_screen.ForEach(delegate(screens item_screens)
			        {
					    query="UPDATE capturescreen SET synchronoused=1 WHERE id="+(int)item_screens.id;
					    try{
					    	connection_item.ExecuteQuery(query);
					    }catch(Exception ex){
					    	Console.WriteLine(ex.ToString());
					    }
			        });
					
					list_item_screen.ForEach(delegate(screens item_screens)
			        {
					    query="UPDATE capturescreen SET synchronoused=1 WHERE id="+(int)item_screens.id;
				    	try{
					    	connection_item.ExecuteQuery(query);
					    }catch(Exception ex){
				    		LogWriter.LogWrite(ex.ToString());
					    	
					    }
				    	
					     String screenshotpng_path = moniter_config.screenshotpng_path()+item_screens.file_name;
				    	ftpClient.upload(moniter_config.screenshotpng_url+item_screens.file_name, screenshotpng_path);
				    	query="UPDATE capturescreen SET uploaded=1 WHERE id="+(int)item_screens.id;
				    	try{
					    	connection_item.ExecuteQuery(query);
					    }catch(Exception ex){
				    		LogWriter.LogWrite(ex.ToString());
					    	
					    }
						
				    	
			        });
					/* Upload a File */

					//ftpClient.upload
					//MessageBox.Show(m);
					//user_item.username=json_user;
				}
				
			}
			 query="DELETE capturescreen  WHERE synchronoused=1 AND uploaded=1";
		    try{
		    	connection_item.ExecuteQuery(query);
		    }catch(Exception ex){
			 	LogWriter.LogWrite(ex.ToString());
		    }
		}

		void Timer_synchronousTick(object sender, EventArgs e)
		{
			
			 Thread th = new Thread(new ThreadStart(synchronous_WorkThread));
	        th.Start();
	        
			

		}
		void Panel_loadingPaint(object sender, PaintEventArgs e)
		{

			
			
    
    
			

		}
		
		void MainFormShown(object sender, EventArgs e)
		{
			
		}
		void MainFormActivated(object sender, EventArgs e)
		{
			
		}
		
	}
}
