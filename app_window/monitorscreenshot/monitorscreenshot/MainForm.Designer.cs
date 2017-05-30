/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 16/10/2016
 * Time: 11:08 SA
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
namespace monitorscreenshot
{
	partial class MainForm
	{
		/// <summary>
		/// Designer variable used to keep track of non-visual components.
		/// </summary>
		private System.ComponentModel.IContainer components = null;
		private System.Windows.Forms.Label lab_name;
		private System.Windows.Forms.Label label1;
		private System.Windows.Forms.Panel panel1;
		private System.Windows.Forms.TextBox textBox1;
		private System.Windows.Forms.PictureBox pictureBox1;
		private System.Windows.Forms.PictureBox btn_play;
		private System.Windows.Forms.Timer start_capture;
		private System.Windows.Forms.Panel panel2;
		private System.Windows.Forms.Panel panel3;
		private System.Windows.Forms.Label label6;
		private System.Windows.Forms.Label label5;
		private System.Windows.Forms.Label label4;
		private System.Windows.Forms.LinkLabel linkLabel1;
		private System.Windows.Forms.Label label3;
		private System.Windows.Forms.Label label2;
		private System.Windows.Forms.Timer timer_synchronous;
		private System.Windows.Forms.Panel panel4;
		private System.Windows.Forms.Panel panel_loading;
		private System.Windows.Forms.PictureBox pictureBox_loading;
		
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
			this.components = new System.ComponentModel.Container();
			this.panel1 = new System.Windows.Forms.Panel();
			this.btn_play = new System.Windows.Forms.PictureBox();
			this.textBox1 = new System.Windows.Forms.TextBox();
			this.start_capture = new System.Windows.Forms.Timer(this.components);
			this.panel2 = new System.Windows.Forms.Panel();
			this.panel3 = new System.Windows.Forms.Panel();
			this.label6 = new System.Windows.Forms.Label();
			this.label5 = new System.Windows.Forms.Label();
			this.label4 = new System.Windows.Forms.Label();
			this.linkLabel1 = new System.Windows.Forms.LinkLabel();
			this.label3 = new System.Windows.Forms.Label();
			this.label2 = new System.Windows.Forms.Label();
			this.timer_synchronous = new System.Windows.Forms.Timer(this.components);
			this.lab_name = new System.Windows.Forms.Label();
			this.label1 = new System.Windows.Forms.Label();
			this.pictureBox1 = new System.Windows.Forms.PictureBox();
			this.panel4 = new System.Windows.Forms.Panel();
			this.panel_loading = new System.Windows.Forms.Panel();
			this.pictureBox_loading = new System.Windows.Forms.PictureBox();
			this.panel1.SuspendLayout();
			((System.ComponentModel.ISupportInitialize)(this.btn_play)).BeginInit();
			this.panel2.SuspendLayout();
			this.panel3.SuspendLayout();
			((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).BeginInit();
			this.panel4.SuspendLayout();
			this.panel_loading.SuspendLayout();
			((System.ComponentModel.ISupportInitialize)(this.pictureBox_loading)).BeginInit();
			this.SuspendLayout();
			// 
			// panel1
			// 
			this.panel1.BackColor = System.Drawing.SystemColors.ControlLight;
			this.panel1.Controls.Add(this.btn_play);
			this.panel1.Controls.Add(this.textBox1);
			this.panel1.Location = new System.Drawing.Point(12, 73);
			this.panel1.Name = "panel1";
			this.panel1.Size = new System.Drawing.Size(571, 59);
			this.panel1.TabIndex = 3;
			// 
			// btn_play
			// 
			this.btn_play.Image = global::monitorscreenshot.Properties.Resource1.play1;
			this.btn_play.Location = new System.Drawing.Point(374, 3);
			this.btn_play.Name = "btn_play";
			this.btn_play.Size = new System.Drawing.Size(50, 50);
			this.btn_play.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.btn_play.TabIndex = 1;
			this.btn_play.TabStop = false;
			this.btn_play.Click += new System.EventHandler(this.btn_playClick);
			// 
			// textBox1
			// 
			this.textBox1.Font = new System.Drawing.Font("Microsoft Sans Serif", 20F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
			this.textBox1.Location = new System.Drawing.Point(10, 10);
			this.textBox1.Margin = new System.Windows.Forms.Padding(10);
			this.textBox1.Name = "textBox1";
			this.textBox1.Size = new System.Drawing.Size(308, 38);
			this.textBox1.TabIndex = 0;
			// 
			// start_capture
			// 
			this.start_capture.Tick += new System.EventHandler(this.Start_captureTick);
			// 
			// panel2
			// 
			this.panel2.BackColor = System.Drawing.SystemColors.ButtonHighlight;
			this.panel2.Controls.Add(this.panel3);
			this.panel2.Controls.Add(this.linkLabel1);
			this.panel2.Controls.Add(this.label3);
			this.panel2.Controls.Add(this.label2);
			this.panel2.Location = new System.Drawing.Point(15, 149);
			this.panel2.Name = "panel2";
			this.panel2.Size = new System.Drawing.Size(567, 178);
			this.panel2.TabIndex = 5;
			// 
			// panel3
			// 
			this.panel3.BackColor = System.Drawing.SystemColors.Control;
			this.panel3.Controls.Add(this.label6);
			this.panel3.Controls.Add(this.label5);
			this.panel3.Controls.Add(this.label4);
			this.panel3.Location = new System.Drawing.Point(302, 25);
			this.panel3.Name = "panel3";
			this.panel3.Size = new System.Drawing.Size(249, 36);
			this.panel3.TabIndex = 3;
			// 
			// label6
			// 
			this.label6.Location = new System.Drawing.Point(200, 10);
			this.label6.Name = "label6";
			this.label6.Size = new System.Drawing.Size(46, 15);
			this.label6.TabIndex = 2;
			this.label6.Text = "0h 33m";
			// 
			// label5
			// 
			this.label5.BackColor = System.Drawing.SystemColors.ControlDarkDark;
			this.label5.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
			this.label5.ForeColor = System.Drawing.SystemColors.ButtonHighlight;
			this.label5.Location = new System.Drawing.Point(147, 10);
			this.label5.Name = "label5";
			this.label5.Size = new System.Drawing.Size(36, 15);
			this.label5.TabIndex = 1;
			this.label5.Text = "New";
			// 
			// label4
			// 
			this.label4.Location = new System.Drawing.Point(12, 10);
			this.label4.Name = "label4";
			this.label4.Size = new System.Drawing.Size(79, 15);
			this.label4.TabIndex = 0;
			this.label4.Text = "note";
			// 
			// linkLabel1
			// 
			this.linkLabel1.Location = new System.Drawing.Point(54, 129);
			this.linkLabel1.Name = "linkLabel1";
			this.linkLabel1.Size = new System.Drawing.Size(100, 23);
			this.linkLabel1.TabIndex = 2;
			this.linkLabel1.TabStop = true;
			this.linkLabel1.Text = "view online";
			// 
			// label3
			// 
			this.label3.Font = new System.Drawing.Font("Microsoft Sans Serif", 40F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
			this.label3.Location = new System.Drawing.Point(30, 67);
			this.label3.Name = "label3";
			this.label3.Size = new System.Drawing.Size(194, 62);
			this.label3.TabIndex = 1;
			this.label3.Text = "00:33";
			// 
			// label2
			// 
			this.label2.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
			this.label2.Location = new System.Drawing.Point(7, 16);
			this.label2.Name = "label2";
			this.label2.Size = new System.Drawing.Size(89, 21);
			this.label2.TabIndex = 0;
			this.label2.Text = "TODAY";
			// 
			// timer_synchronous
			// 
			this.timer_synchronous.Interval = 1200;
			this.timer_synchronous.Tick += new System.EventHandler(this.Timer_synchronousTick);
			// 
			// lab_name
			// 
			this.lab_name.Font = new System.Drawing.Font("Microsoft Sans Serif", 20F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
			this.lab_name.Location = new System.Drawing.Point(3, 7);
			this.lab_name.Name = "lab_name";
			this.lab_name.Size = new System.Drawing.Size(315, 40);
			this.lab_name.TabIndex = 0;
			this.lab_name.Text = "your name";
			// 
			// label1
			// 
			this.label1.Font = new System.Drawing.Font("Microsoft Sans Serif", 10F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
			this.label1.Location = new System.Drawing.Point(324, 24);
			this.label1.Name = "label1";
			this.label1.Size = new System.Drawing.Size(100, 23);
			this.label1.TabIndex = 1;
			this.label1.Text = "company name";
			// 
			// pictureBox1
			// 
			this.pictureBox1.Image = global::monitorscreenshot.Properties.Resource1.setting;
			this.pictureBox1.Location = new System.Drawing.Point(512, 6);
			this.pictureBox1.Name = "pictureBox1";
			this.pictureBox1.Size = new System.Drawing.Size(30, 30);
			this.pictureBox1.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pictureBox1.TabIndex = 4;
			this.pictureBox1.TabStop = false;
			// 
			// panel4
			// 
			this.panel4.Controls.Add(this.pictureBox1);
			this.panel4.Controls.Add(this.label1);
			this.panel4.Controls.Add(this.lab_name);
			this.panel4.Location = new System.Drawing.Point(11, 6);
			this.panel4.Name = "panel4";
			this.panel4.Size = new System.Drawing.Size(571, 67);
			this.panel4.TabIndex = 6;
			// 
			// panel_loading
			// 
			this.panel_loading.Controls.Add(this.pictureBox_loading);
			this.panel_loading.Location = new System.Drawing.Point(0, 7);
			this.panel_loading.Name = "panel_loading";
			this.panel_loading.Size = new System.Drawing.Size(595, 320);
			this.panel_loading.TabIndex = 1;
			this.panel_loading.Paint += new System.Windows.Forms.PaintEventHandler(this.Panel_loadingPaint);
			// 
			// pictureBox_loading
			// 
			this.pictureBox_loading.Image = global::monitorscreenshot.Properties.Resource1.loading;
			this.pictureBox_loading.Location = new System.Drawing.Point(223, 106);
			this.pictureBox_loading.Name = "pictureBox_loading";
			this.pictureBox_loading.Size = new System.Drawing.Size(100, 100);
			this.pictureBox_loading.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
			this.pictureBox_loading.TabIndex = 0;
			this.pictureBox_loading.TabStop = false;
			// 
			// MainForm
			// 
			this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
			this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
			this.ClientSize = new System.Drawing.Size(595, 332);
			this.Controls.Add(this.panel_loading);
			this.Controls.Add(this.panel1);
			this.Controls.Add(this.panel4);
			this.Controls.Add(this.panel2);
			this.MaximizeBox = false;
			this.MinimizeBox = false;
			this.Name = "MainForm";
			this.Text = "monitorscreenshot";
			this.Activated += new System.EventHandler(this.MainFormActivated);
			this.Load += new System.EventHandler(this.MainFormLoad);
			this.Shown += new System.EventHandler(this.MainFormShown);
			this.panel1.ResumeLayout(false);
			this.panel1.PerformLayout();
			((System.ComponentModel.ISupportInitialize)(this.btn_play)).EndInit();
			this.panel2.ResumeLayout(false);
			this.panel3.ResumeLayout(false);
			((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).EndInit();
			this.panel4.ResumeLayout(false);
			this.panel4.PerformLayout();
			this.panel_loading.ResumeLayout(false);
			this.panel_loading.PerformLayout();
			((System.ComponentModel.ISupportInitialize)(this.pictureBox_loading)).EndInit();
			this.ResumeLayout(false);

		}
	}
}
