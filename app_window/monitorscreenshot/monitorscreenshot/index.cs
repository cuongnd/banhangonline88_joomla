/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 17/10/2016
 * Time: 9:22 CH
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using monitorscreenshot.libraries;

namespace monitorscreenshot
{
	/// <summary>
	/// Description of index.
	/// </summary>
	public class index
	{
		public index()
		{
		}
		public static void init(){
			setting.load_setting();
			
		}
	}
}
