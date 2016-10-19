/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 16/10/2016
 * Time: 10:13 CH
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Drawing.Imaging;
using System.IO;


namespace monitorscreenshot
{
	/// <summary>
	/// Description of utility.
	/// </summary>
	public class utility
	{
		public utility()
		{
		}
		public static Bitmap GrayScale(Bitmap Bmp)
		{
		    int rgb;
		    Color c;
		
		    for (int y = 0; y < Bmp.Height; y++)
		    for (int x = 0; x < Bmp.Width; x++)
		    {
		        c = Bmp.GetPixel(x, y);
		        rgb = (int)((c.R + c.G + c.B) / 3);
		        Bmp.SetPixel(x, y, Color.FromArgb(rgb, rgb, rgb));
		    }
		    return Bmp;
		}
		public static String Base64Encode(String plainText) {
		  var plainTextBytes = System.Text.Encoding.UTF8.GetBytes(plainText);
		  return System.Convert.ToBase64String(plainTextBytes);
		}
		public static String Base64Decode(String base64EncodedData) {
		  var base64EncodedBytes = System.Convert.FromBase64String(base64EncodedData);
		  return System.Text.Encoding.UTF8.GetString(base64EncodedBytes);
		}
	
	}
}
