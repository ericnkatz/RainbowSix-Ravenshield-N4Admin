using System;
using System.Collections;
using System.Xml;
using System.Xml.Serialization;
namespace RvS.Statistics
{
	public class RoundStatistics
	{
		[XmlAttribute()]
		public float XMLLoggerVersion=1.0f;
		public ServerInfo ServerInfo = new ServerInfo();
		[XmlArray()]
		[XmlArrayItem("PlayerInfo", typeof(PlayerInfo))]
		public ArrayList PlayerInfoSet = new ArrayList();
		[XmlArray()]
		[XmlArrayItem("KillInfo", typeof(KillInfo))]
		public ArrayList KillInfoSet = new ArrayList();
		public TerroristStatistics TerroristStatistics
			= new TerroristStatistics();
		public DeviceStatistics DeviceStatistics
			= new DeviceStatistics();
		public static RoundStatistics Load(string path)
		{
			XmlReader reader = new XmlTextReader(path);
			RoundStatistics result = Load(reader);
			reader.Close();
			return result;
		}
		public static RoundStatistics Load(XmlReader reader)
		{
			XmlSerializer serializer =
				new XmlSerializer(typeof(RoundStatistics));
			return (RoundStatistics)serializer.Deserialize(reader);
		}
		public void Save(string path)
		{
			XmlTextWriter writer = new XmlTextWriter(path,
				System.Text.Encoding.Unicode);
			writer.Formatting = Formatting.Indented;
			Save(writer);
			writer.Flush();
			writer.Close();
		}
		public void Save(XmlWriter writer)
		{
			XmlSerializer serializer = new XmlSerializer(GetType());
			serializer.Serialize(writer, this);
		}
	}
	public class ServerInfo
	{
		[XmlAttribute()]
		public string Mod="RavenShield";
		[XmlAttribute()]
		public string Version="PATCH 1.60 (build 412)";
		[XmlAttribute()]
		public string GameType="RGM_TerroristHuntCoopMode";
		[XmlAttribute()]
		public string Map="Peaks";
		[XmlAttribute()]
		public bool PunkBuster=false;
		[XmlAttribute()]
		public int MaxPlayers=16;
		[XmlAttribute()]
		public bool NeedsPassword=false;
		[XmlAttribute()]
		public int RoundTime=180;
		[XmlAttribute()]
		public int BombTime=45;
		[XmlAttribute()]
		public int TerroristCount=32;
	}
	public class PlayerInfo
	{
		[XmlAttribute()]
		public string UbiID=null;
		[XmlAttribute()]
		public string NickName=null;
		[XmlAttribute()]
		public string GlobalID=null;
		[XmlAttribute()]
		public int Hits=0;
		[XmlAttribute()]
		public int Fired=0;
		[XmlAttribute()]
		public bool Played=false;
		[XmlAttribute()]
		public int DoorsDestroyed=0;
		[XmlAttribute()]
		public int BombsArmed=0;
		[XmlAttribute()]
		public int BombsDisarmed=0;
		[XmlAttribute()]
		public int DevicesActivated=0;
		[XmlAttribute()]
		public int DevicesDeactivated=0;
		[XmlAttribute()]
		public string LastKillerUbiID;
		public KillInfo Kills = new KillInfo();
		public KillInfo Deaths = new KillInfo();
	}
	public class TerroristStatistics
	{
		public KillInfo Kills = new KillInfo();
		public KillInfo Deaths = new KillInfo();
	}
	public class KillInfo
	{
		[XmlAttribute()]
		public string Killer=null;
		[XmlAttribute()]
		public string Killed=null;
		[XmlAttribute()]
		public bool FriendlyFire=false;
		[XmlAttribute()]
		public bool NeutralFire=false;
	}
	public class DeviceStatistics
	{
		[XmlAttribute()]
		public int DoorsDestroyed=0;
		[XmlAttribute()]
		public int DevicesDestroyed=0;
		[XmlAttribute()]
		public int BombsDetonated=0;
	}
}
