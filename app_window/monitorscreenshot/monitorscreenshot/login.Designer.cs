/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 18/10/2016
 * Time: 5:35 SA
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
namespace monitorscreenshot
{
	partial class login
	{
		/// <summary>
		/// Designer variable used to keep track of non-visual components.
		/// </summary>
		private System.ComponentModel.IContainer components = null;
		private System.Windows.Forms.Label label1;
		private System.Windows.Forms.TextBox txtusername;
		private System.Windows.Forms.TextBox txtpassword;
		private System.Windows.Forms.Label label2;
		private System.Windows.Forms.LinkLabel linkLabel1;
		private System.Windows.Forms.LinkLabel linkLabel2;
		private System.Windows.Forms.Button btn_login;
		
		/// <summary>
		/// Disposes resources used by the form.
		/// </summary>
		/// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
		protected override void Dispose(bool disposing)
		{
			if (disposing) {
				if (components != null) {
					components.Dispose();
				}
			}
			base.Dispose(disposing);
		}
		
		/// <summary>
		/// This method is required for Windows Forms designer support.
		/// Do not change the method contents inside the source code editor. The Forms designer might
		/// not be able to load this method if it was changed manually.
		/// </summary>
		private void InitializeComponent()
		{
			this.label1 = new System.Windows.Forms.Label();
			this.txtusername = new System.Windows.Forms.TextBox();
			this.txtpassword = new System.Windows.Forms.TextBox();
			this.label2 = new System.Windows.Forms.Label();
			this.linkLabel1 = new System.Windows.Forms.LinkLabel();
			this.linkLabel2 = new System.Windows.Forms.LinkLabel();
			this.btn_login = new System.Windows.Forms.Button();
			this.SuspendLayout();
			// 
			// label1
			// 
			this.label1.Location = new System.Drawing.Point(33, 81);
			this.label1.Name = "label1";
			this.label1.Size = new System.Drawing.Size(100, 23);
			this.label1.TabIndex = 0;
			this.label1.Text = "User name";
			// 
			// txtusername
			// 
			this.txtusername.Location = new System.Drawing.Point(139, 78);
			this.txtusername.Name = "txtusername";
			this.txtusername.Size = new System.Drawing.Size(100, 20);
			this.txtusername.TabIndex = 1;
			// 
			// txtpassword
			// 
			this.txtpassword.Location = new System.Drawing.Point(139, 101);
			this.txtpassword.Name = "txtpassword";
			this.txtpassword.PasswordChar = '*';
			this.txtpassword.Size = new System.Drawing.Size(100, 20);
			this.txtpassword.TabIndex = 3;
			// 
			// label2
			// 
			this.label2.Location = new System.Drawing.Point(33, 104);
			this.label2.Name = "label2";
			this.label2.Size = new System.Drawing.Size(100, 23);
			this.label2.TabIndex = 2;
			this.label2.Text = "Password";
			// 
			// linkLabel1
			// 
			this.linkLabel1.Location = new System.Drawing.Point(153, 128);
			this.linkLabel1.Name = "linkLabel1";
			this.linkLabel1.Size = new System.Drawing.Size(100, 23);
			this.linkLabel1.TabIndex = 4;
			this.linkLabel1.TabStop = true;
			this.linkLabel1.Text = "forgot user name";
			// 
			// linkLabel2
			// 
			this.linkLabel2.Location = new System.Drawing.Point(153, 151);
			this.linkLabel2.Name = "linkLabel2";
			this.linkLabel2.Size = new System.Drawing.Size(100, 23);
			this.linkLabel2.TabIndex = 5;
			this.linkLabel2.TabStop = true;
			this.linkLabel2.Text = "forgot password";
			// 
			// btn_login
			// 
			this.btn_login.Location = new System.Drawing.Point(163, 198);
			this.btn_login.Name = "btn_login";
			this.btn_login.Size = new System.Drawing.Size(75, 23);
			this.btn_login.TabIndex = 6;
			this.btn_login.Text = "Login";
			this.btn_login.UseVisualStyleBackColor = true;
			this.btn_login.Click += new System.EventHandler(this.Btn_loginClick);
			// 
			// login
			// 
			this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
			this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
			this.ClientSize = new System.Drawing.Size(284, 262);
			this.Controls.Add(this.btn_login);
			this.Controls.Add(this.linkLabel2);
			this.Controls.Add(this.linkLabel1);
			this.Controls.Add(this.txtpassword);
			this.Controls.Add(this.label2);
			this.Controls.Add(this.txtusername);
			this.Controls.Add(this.label1);
			this.MaximizeBox = false;
			this.Name = "login";
			this.Text = "login";
			this.ResumeLayout(false);
			this.PerformLayout();

		}
	}
}
