/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 18/10/2016
 * Time: 5:35 SA
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Drawing;
using System.Windows.Forms;
using monitorscreenshot.libraries;
using monitorscreenshot.Properties;

namespace monitorscreenshot
{
	/// <summary>
	/// Description of login.
	/// </summary>
	public partial class login : Form
	{
		public login()
		{
			//
			// The InitializeComponent() call is required for Windows Forms designer support.
			//
			InitializeComponent();
			
			//
			// TODO: Add constructor code after the InitializeComponent() call.
			//
		}
		void Btn_loginClick(object sender, EventArgs e)
		{
			var username=txtusername.Text;
			var password=txtpassword.Text;
			if(username.Trim()=="")
			{
				MessageBox.Show(Properties.Settings1.Default.INPUT_USERNAME);
				txtusername.Focus();
				return;
			}else if(password.Trim()=="")
			{
				MessageBox.Show(Properties.Settings1.Default.INPUT_PASSWORD);
				txtpassword.Focus();
				return;
			}
			if(!user.ajax_remote_check_exists_user(username,password)){
				txtusername.Focus();
			}else{
				setting.save_user_info(username,password);
				this.Close();
			}
		}
	}
}
