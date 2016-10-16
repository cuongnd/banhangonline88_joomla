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
using System.Net;
using System.Windows.Forms;
namespace monitorscreenshot
{
	/// <summary>
	/// Description of MainForm.
	/// </summary>
	public partial class MainForm : Form
	{
		Boolean state_capture=false;
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
				//Create a new bitmap.
				String screenshotpng = "Screenshot.png";
				using (var bmpScreenshot = new Bitmap(Screen.PrimaryScreen.Bounds.Width, Screen.PrimaryScreen.Bounds.Height, PixelFormat.Format32bppArgb)) {
					var gfxScreenshot = Graphics.FromImage(bmpScreenshot);
					gfxScreenshot.CopyFromScreen(Screen.PrimaryScreen.Bounds.X, Screen.PrimaryScreen.Bounds.Y, 0, 0, Screen.PrimaryScreen.Bounds.Size, CopyPixelOperation.SourceCopy);
					bmpScreenshot.Save(screenshotpng, ImageFormat.Png);
					
					
					
				}
				/* Create Object Instance */
				ftp ftpClient = new ftp(@"ftp://103.45.230.226/", "banhangonl", "IMhySiU0V");
				
				/* Upload a File */
				ftpClient.upload("/domains/banhangonline88.com/public_html/images/capturescreen/play1.png", @"C:\Users\cuongnd\Desktop\play1.png");
			}

// Create a graphics object from the bitmap.

// Take the screenshot from the upper left corner to the right bottom corner.

// Save the screenshot to the specified path that the user has chosen.

	
		}
		void Start_captureTick(object sender, EventArgs e)
		{
			
		}
	}
}
