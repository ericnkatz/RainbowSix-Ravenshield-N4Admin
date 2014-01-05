//// Mirc Script by ^Kempi and TAT-Neo /////

// Name change Code.
on 1:NICK:{
  if ($nick != $readini(RvSBot.ini,Bot,Nick) ) {
  halt
  }
  else {
    /writeini RvSBot.ini BOT Nick $newnick
  }
}

// Menu Commands

menu nicklist  { 
  Server Commands
  .Server Stuff
  ..Say:/ctcp $readini(RvSBot.ini,Bot,Nick) say $readini(RvSBot.ini,Bot,Password) $$?="What you want to say?"
  ..Bot Say: /ctcp $readini(RvSBot.ini,Bot,Nick) RAW $readini(RvSBot.ini,Bot,Password) PRIVMSG $readini(RvSBot.ini,Bot,Nick) :SAY $readini(RvSBot.ini,Bot,Password) $$?="Message?" 
  ..-
  ..Show Scores:/ctcp $readini(RvSBot.ini,Bot,Nick) showscores $readini(RvSBot.ini,Bot,Password)
  ..Restart Round:/ctcp $readini(RvSBot.ini,Bot,Nick) restartround $readini(RvSBot.ini,Bot,Password)
  ..Restart Round w/reason:/ctcp $readini(RvSBot.ini,Bot,Nick) restartround $readini(RvSBot.ini,Bot,Password) $$?="Reason why?"
  ..Restart Match:/ctcp $readini(RvSBot.ini,Bot,Nick) restartmatch $readini(RvSBot.ini,Bot,Password)
  ..Restart Match w/reason:/ctcp $readini(RvSBot.ini,Bot,Nick) restartmatch $readini(RvSBot.ini,Bot,Password) $$?="Reason why?"
  ..-
  ..Restart Server:/ctcp $readini(RvSBot.ini,Bot,Nick) restartserver $readini(RvSBot.ini,Bot,Password)
  ..Restart Server w/reason:/ctcp $readini(RvSBot.ini,Bot,Nick) restartserver $readini(RvSBot.ini,Bot,Password) $$?="Reason why?"
  ..-
  ..Change Map:/ctcp $readini(RvSBot.ini,Bot,Nick) map $readini(RvSBot.ini,Bot,Password) $$?="Map #?" $$?="Reason?"
  ..-
  ..Kick Name:/ctcp $readini(RvSBot.ini,Bot,Nick) kick $readini(RvSBot.ini,Bot,Password) $$?="Username?"
  ..Ban Name:/ctcp $readini(RvSBot.ini,Bot,Nick) ban $readini(RvSBot.ini,Bot,Password) $$?="Username?"
  .Bot Commands
  ..Say Chan:/ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) PRIVMSG $chan : $+ $$?=|"Messages goes here"
  ..Say User:/ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) PRIVMSG $$?="Who to?" : $+ $$?=|"Messages goes here"
  ..Action Chan:/ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) PRIVMSG $chan :ACTION  $$?=|"Messages goes here" 
  ..Action User:/ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) PRIVMSG $$?="Who to?" :ACTION  $$?=|"Messages goes here" 
  ..-
  ..Join Chan:/ctcp $readini(RvSBot.ini,Bot,Nick) RAW $readini(RvSBot.ini,Bot,Password) JOIN #$$?="Channel?"
  ..Part Chan:/ctcp $readini(RvSBot.ini,Bot,Nick) RAW $readini(RvSBot.ini,Bot,Password) PART #$$?="Channel?"
  ..-
  ..Change Bots Nick: {
              /set %BotName $$?=|"NewBotName"
              /ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) NICK %BotName
              /writeini RvSBot.ini BOT Nick %BotName
  }  
  ..-
  .Timed Messages
  ..Start Message:/timersserver 0 $$?="Intervul in Seconds?" /ctcp $readini(RvSBot.ini,Bot,Nick) say $readini(RvSBot.ini,Bot,Password) $readini(RvSBot.ini,Bot,Message)
  ..Stop Message:/timersserver off
  ..-
  ..Set Message:/writeini RvSBot.ini BOT Message $$?="What message?"
  .Slaps
  ..Pimp Slap:/ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) PRIVMSG $chan :ACTION Pimp Slaps $$1.
  ..Trout Slap:/ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) PRIVMSG $chan :ACTION Slaps $$1 around with a large trout.
  ..Is Stupid:/ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) PRIVMSG $chan :ACTION Thinks $$1 is so stupid his blonde sister has to start up his computer.
  ..Ownage:/ctcp $readini(RvSBot.ini,Bot,Nick) raw $readini(RvSBot.ini,Bot,Password) PRIVMSG $chan :ACTION Thinks $$1 sucks at RvS, Because my blonde sister beats him on 1 v 1.
  .Options 
  ..Set Password:/writeini RvSBot.ini BOT Password $$?="What password?"
  ..Set Bot Name:/writeini RvSBot.ini BOT Nick $$?="Whats the bots name?"
  ..Set Bot w/right click:/writeini RvSBot.ini BOT Nick $$1
}

//// End of Script

//// Join messages - Remove the // in the 9 lines below to make active.
//on *:text:*JOHNDOE has joined this server*:#: {
//  if ($nick != $readini(RvSBot.ini,Bot,Nick) ) {
//  halt
//  }
//  else {
//    /ctcp $readini(RvSBot.ini,Bot,Nick) SAY $readini(RvSBot.ini,Bot,Password) JOHNDOE change your name please!
//  }
//  halt
//}
