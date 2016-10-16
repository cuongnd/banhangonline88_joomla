/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 16/10/2016
 * Time: 11:08 SA
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Drawing;
using System.Drawing.Imaging;
using System.IO;
using System.Net;
using System.Windows.Forms;
using System.Net.Http;
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
		void Start_captureTick(object sender, EventArgs e)
		{
			total_capture++;
			String str_now= DateTime.Now.ToString("H_mm_ss_dd_MM_yyyy");
			if(prev_str_now=="")
			{
				prev_str_now=str_now;
			}
			String file_capture_name=prev_str_now+"_"+str_now+".png";
			prev_str_now=str_now;
			String result = System.IO.Path.GetTempPath();
				//Create a new bitmap.
			String screenshotpng_path = result+file_capture_name; // @"C:\Users\cuongnd\Desktop\Screenshot.png";
			var bmpScreenshot = new Bitmap(Screen.PrimaryScreen.Bounds.Width, Screen.PrimaryScreen.Bounds.Height, PixelFormat.Format32bppArgb);
			var gfxScreenshot = Graphics.FromImage(bmpScreenshot);
					gfxScreenshot.CopyFromScreen(Screen.PrimaryScreen.Bounds.X, Screen.PrimaryScreen.Bounds.Y, 0, 0, Screen.PrimaryScreen.Bounds.Size, CopyPixelOperation.SourceCopy);
					bmpScreenshot=utility.GrayScale(bmpScreenshot);
					bmpScreenshot.Save(screenshotpng_path, ImageFormat.Png);
			

			/* Create Object Instance */
			
			
			/* Upload a File */
			ftpClient.upload("/domains/banhangonline88.com/public_html/images/capturescreen/"+file_capture_name, screenshotpng_path);

			
			

			WebRequest wrGETURL;
			wrGETURL = WebRequest.Create("https://api.somewhere.com/desk/external_api/v1/customers.json");
			wrGETURL.Method = "GET";
			wrGETURL.ContentType = "application/json"; 
			wrGETURL.Credentials = new NetworkCredential("x", "reallylongstring");
			Stream objStream = wrGETURL.GetResponse().GetResponseStream();
			var objReader = new StreamReader(objStream);
			String responseFromServer = objReader.ReadToEnd();



			if(total_capture==2)
			{
				state_capture=false;
				start_capture.Enabled=state_capture;
				MessageBox.Show(screenshotpng_path);
				
			}
			
			
		}
	}
}
