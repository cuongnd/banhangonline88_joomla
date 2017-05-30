/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 18/10/2016
 * Time: 11:09 CH
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;

namespace monitorscreenshot.libraries
{
	/// <summary>
	/// Description of screens.
	/// </summary>
	public class screens
	{
		public int id=0;
		public String create_on="";
		public String file_name="";
		public int user_id=0;
		public int synchronoused=0;
		public int uploaded=0;
		private volatile static screens uniqueInstance;
		public static screens getInstance()
		{
			if (uniqueInstance == null)
	        {
				uniqueInstance = new screens();
	        }
	        return uniqueInstance;
        
		}
		public screens()
		{
		}
	}
}
