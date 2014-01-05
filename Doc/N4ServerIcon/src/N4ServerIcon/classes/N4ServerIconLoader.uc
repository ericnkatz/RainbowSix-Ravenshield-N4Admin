/*
    Filename: N4ServerIconLoader.uc
    Author: Neil Popplewell
    Email: neo@koalaclaw.com

   Copyright © 2004 Neil Popplewell.  All rights reserved.
*/
class N4ServerIconLoader extends Mutator;

const MaxIconsPerLoaded = 4;
struct N4Point
{
  var() config float X;
  var() config float Y;
};
struct N4IconInfo
{
  var() config Material icon;
  var() config N4Point Pos;
  var() config N4Point StartPos;
  var() config N4Point DrawSize;
  var() config N4Point Size;
  var() config color color;
  var() config float RotationAngle;
  var() config bool bShowWhileAlive;
  var() config bool bDoNotShowWhileDead;
  var() config float StartUpTime; //Time for it to start to fade in
  var() config float FadeInTime;
  var() config float FadeOutTime;
  var float CurrentAlpha;
  var float CurrentStartUpTime;
};
var const string ServerInfomation;
var const string ClientInfomation;
var const string WebSiteInfomation;
var const string CopyRightNotice;


var protected const string InteractionClass;


var() config N4IconInfo IconInfo[MaxIconsPerLoaded];
var() config bool bTakeFocus;
//Server side repped variables:
var N4IconInfo IconInfoRepped[MaxIconsPerLoaded];
var bool bTakeFocusRepped;

//Client side only variables:
var N4ServerIconInteraction ServerIcon;
var bool bTryToLoadIcon;
var Color ConsoleMessageColor;

replication
{
  reliable if (Role==ROLE_Authority)
           IconInfoRepped,bTakeFocusRepped;
}
function BeginPlay()
{
  local int i;
  super.BeginPlay();
  bTakeFocusRepped=bTakeFocus;
  for(i=0;i<MaxIconsPerLoaded;i++)
  {
    IconInfoRepped[i]=IconInfo[i];
  }
  Log("");
  Log(ServerInfomation@CopyRightNotice);
  Log(WebSiteInfomation);
  Log("");
}
simulated event PostNetBeginPlay()
{
  super.PostNetBeginPlay();
  if(Level.NetMode == NM_DedicatedServer) return; //Dont spawn the icon on the server
  AddMessageToConsole(ClientInfomation@CopyRightNotice,ConsoleMessageColor);
  AddMessageToConsole(WebSiteInfomation,ConsoleMessageColor);
  Log(ClientInfomation@CopyRightNotice);
  Log(WebSiteInfomation);
  bTryToLoadIcon=true;
}

simulated event Tick(float t)
{
  local PlayerController pc;
  local ViewPort player,iPlayer;
  local Interaction interaction;
  super.Tick(t);
  if(bTryToLoadIcon)
  {
    foreach DynamicActors(class'PlayerController',pc)
    {
      player=Viewport(pc.Player);
      if(player != none) break;
    }
    if(player == none || player.InteractionMaster == none) return;
    bTryToLoadIcon=false;
    iPlayer=player;
    interaction=player.InteractionMaster.AddInteraction(InteractionClass,iPlayer);
    if(interaction != none && iPlayer == none) //Do this if it is global
    {
      interaction.ViewPortOwner = player;
    }
    ServerIcon=N4ServerIconInteraction(interaction);
    if(ServerIcon == none)
    {
      if(interaction != none)
      {
        player.InteractionMaster.RemoveInteraction(interaction);
      }
      interaction=none;
    }
    else
    {
      ServerIcon.InitializeIcon(IconInfoRepped,bTakeFocusRepped);
    }
  }
}

event Destroyed()
{
  super.Destroyed();
  if(ServerIcon != none)
  {
    ServerIcon.RemoveSelf();
  }
  ServerIcon=none;
}
defaultproperties
{
  bTakeFocus=false
  bAlwaysTick=true
  bAlwaysRelevant=true
  RemoteRole=ROLE_SimulatedProxy
  InteractionClass="N4ServerIcon.N4ServerIconInteraction"
  CopyRightNotice="Copyright © 2004 Neil Popplewell.  All rights reserved."
  ClientInfomation="This server is running N4ServerIcon v0.91b by Neo4E656F."
  WebSiteInfomation="More infomation on N4ServerIcon can be found at www.koalaclaw.com."
  ServerInfomation="N4ServerIcon v0.91b by Neo4E656F loaded."
  ConsoleMessageColor=(R=255,G=255,B=0,A=255)
  IconInfo[0]=(Icon=None,DrawSize=(X=32,Y=32))
  IconInfo[1]=(Icon=None,DrawSize=(X=32,Y=32))
  IconInfo[2]=(Icon=None,DrawSize=(X=32,Y=32))
  IconInfo[3]=(Icon=None,DrawSize=(X=32,Y=32))
}
