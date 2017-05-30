/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 16/10/2016
 * Time: 11:08 SA
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Windows.Forms;

namespace monitorscreenshot
{
	/// <summary>
	/// Class with program entry point.
	/// </summary>
	internal sealed class Program
	{
		/// <summary>
		/// Program entry point.
		/// </summary>
		[STAThread]
		private static void Main(string[] args)
		{
			Application.EnableVisualStyles();
			Application.SetCompatibleTextRenderingDefault(false);
			var form_item=new MainForm();
			
			Application.Run(form_item);
			
			
			
		}
		
	}
}
