��    S      �  q   L        {     
   �     �  �  �     <	     D	     V	  *   k	     �	     �	  (  �	     �
  &   �
          *  ?   1     q  !   }     �  r   �  w        �     �     �     �     �               ;     N     j     }     �     �     �     �     �  4   �     .     G     [     m     }     �     �  
   �  p   �     *     9     H     W     c  +   r     �  J  �  M       R     U     u  B   z     �  �   �  
   �  .   �  d   �  S   T  6  �  0  �               0     K     W  .   ^     �  
   �  �  �  .   !  
   P  �   [            �     n   �     .  0   >  q  o     �     �     �  )        B     P  G  d     �     �     �     �  )   �           &      D   �   Q   �   �      S!     j!     x!  $   �!     �!     �!     �!     "     ""     9"     P"     g"     y"     �"  	   �"     �"  9   �"     	#     $#     :#  	   U#  &   _#     �#     �#     �#  u   �#     6$     E$     T$     c$     o$  .   ~$     �$     �$     �$     %  -   	%     7%     ?%     S%  �   a%  
   &     (&  &   7&      ^&  0  &  *  �'  	   �(     �(     )     !)     /)     5)     J)  
   R)  �  ])     �*     �*  ~   �*     y+     |+            '       .       O       A   -      C      #      2      9              (                 	   %   =   <          Q   G         ,           N      F      !          
          0       I   3   )       D      @   :                       8              &   >         4       M   *   E      ;   P       $      /   K       +      R             7           S   1   ?               5   H           B       J   L   6   "    A connection to Asterisk Manager could not be made. This module requires Asterisk to be running and have proper credentials AGI Lookup AGI Result Variable AGI to use to obtain the result (it must return text only, no html, xml or json. For example test.agi,param1,param2 The following substitutions are available for use in the input parameters:<br>[NUMBER] the callerid number<br>[INPUT] the dtmf sequence input<br>[DID] the dialed number<br>[xxx] where xxx is the name of an input or result variable saved from a previous dynamic route on the same call Actions Add Dynamic Route Allows input of DTMF Amount of time in seconds for dtmf timeout Announcement Asterisk Variable Asterisk variable whose value is to be used. The following substitutions are available:<br>[NUMBER] the callerid number<br>[INPUT] the dtmf sequence input<br>[DID] the dialed number<br>[xxx] where xxx is the name of an input or result variable saved from a previous dynamic route on the same call Connection Error Database to connect to on MySQL server Default Destination Delete Delete this entry. Dont forget to click Submit to save changes! Description Description of this Dynamic Route Destination Destination to send the call to if the dtmf did not match the validation rule and maximum retries has been reached Destination to send the call to if there is no match in the Dynamic Route Entries section below or if the lookup fails. Dyname Route Name Dynamic Route Dynamic Route DTMF Options Dynamic Route Default Entry Dynamic Route Description Dynamic Route Entries Dynamic Route General Options Dynamic Route List Dynamic Route Lookup Source Dynamic Route Name Dynamic Route Saved Variables Dynamic Route: %s Dynamic Route: %s / Option: %s Edit Dynamic Route:  Edit: Enable DTMF Input Greeting to be played on entry to the Dynamic Route. Hostname of MySQL server Invalid Destination Invalid Recording Invalid Retries Invalid Retry Recording List Dynamc Routes Match Max digits Maximum number of DTMF digits. If zero then no limit. Avoids having to press # key at end of fixed input length. MySQL database MySQL hostname MySQL password MySQL query MySQL username Name of result variable used in AGI script. Name of this Dynamic Route Name of variable in which to save dtmf input for future use in the dialplan or further dynamic routes. This is available as [xxx] in the query/lookup where xxx is the name of the variable you specify here. To use the variable in the dialplan (e.g. custom applicaitons) it is necessary to prefix it with DYNROUTE_ e.g. DYNROUTE_xxx Name of variable in which to save lookup result for future use in the dialplan or further dynamic routes. This is available as [xxx] in the query/lookup where xxx is the name of the variable you specify here. To use the variable in the dialplan (e.g. custom applicaitons) it is necessary to prefix it with DYNROUTE_ e.g. DYNROUTE_xxx No No Astman, Not loading Dynroute None Number of times to retry when dtmf does not match validation rules ODBC Function ODBC Function to use. The value used here should be the name of a section in /etc/asterisk/func_odbc.conf. If checking whether the function is registered at the asterisk console with "core show functions " it has an ODBC_ prepended.  ODBC query Password to use for connection to MySQL server Prompt to be played if dtmf does not match validation rules and maximum retries has not been reached Prompt to be played when a timeout occurs, before prompting the caller to try again Query to use to obtain the result from the MySQL database. The following substitutions are available:<br>[NUMBER] the callerid number<br>[INPUT] the dtmf sequence input<br>[DID] the dialed number<br>[xxx] where xxx is the name of an input or result variable saved from a previous dynamic route on the same call Query to use to obtain the result from the database. The following substitutions are available:<br>[NUMBER] the callerid number<br>[INPUT] the dtmf sequence input<br>[DID] the dialed number<br>[xxx] where xxx is the name of an input or result variable saved from a previous dynamic route on the same call Reset Saved input variable name Saved result variable name Source Type Submit The source of the information to be looked up. Timeout URL Lookup URL to use to obtain the result (it must return text only, no html, xml or json. Exmaple http://localhost/test.php?param1=4&param2=9 The following substitutions are available:<br>[NUMBER] the callerid number<br>[INPUT] the dtmf sequence input<br>[DID] the dialed number<br>[xxx] where xxx is the name of an input or result variable saved from a previous dynamic route on the same call Username to use for connection to MySQL server Validation Validation rules using a Asterisk regular expression (see Asterisk REGEX_MATCH). For example to ensure the input is between 3 and 4 digits long you could use ^[0-9]\{3,4\}$ Yes value to be matched Project-Id-Version: FreePBX dynroute module
Report-Msgid-Bugs-To: 
PO-Revision-Date: 2020-12-19 05:12+0100
Last-Translator: John Fawcett <john@voipsupport.it>
Language-Team: John Fawcett <john@voipsupport.it>
Language: it_IT
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 2.4.2
Plural-Forms: nplurals=2; plural=(n != 1);
X-Poedit-SourceCharset: UTF-8
 Non è possibile connettere a Asterisk Manager. Asterisk deve essere attivo e le credenziali di accesso valide Nome script AGI AGI Nome del variabile che contiene il risultato Nome script AGI da eseguire per ottenere il risultato (solo testo, no html, xml o json). Ad esempio test.agi,param1,param2. Si possono utilizzare queste sostituzioni<br>[NUMBER] callerid<br>[INPUT] dtmf<br>[DID] numero chiamato<br>[xxx] dove xxx è il nome di un variabile di input o di risultato salvato da un precedente dynamic route utilizzato sulla stessa chiamata. Azioni Aggiungi Dynamic Route Consente l'input di DTMF Tempo in secondi per aspettare input DTMF Registrazione Variabile Asterisk  Nome del variable o espressione Asterisk da utilizzare per ottenere il risultato. Si possono utilizzare queste sostituzioni<br>[NUMBER] callerid<br>[INPUT] dtmf<br>[DID] numero chiamato<br>[xxx] dove xxx è il nome di un variabile di input o di risultato salvato da un precedente dynamic route utilizzato sulla stessa chiamata. Errore di connessione Nome database Destinazione di default Elimina Cancella. Ricorda di salvare la modifica! Descrizione Descrizione del Dynamic Route Destinazione Destinazione quando il dtmf è invalido rispetto alla regola di validazione ed è stato raggiunto il numero massimo di tentativi Destinazione quando non c'è nessuna corrispondenza fra il risultato e i valori nella sezione sottostante o se la query fallisce Nome del Dynamic Route Dynamic Route Dynamic Route Opzioni DTMF Dynamic Route Destination di Default Dynamic Route Descrizione Dynamic Route Destinazioni Dynamic Route Opzioni Generali Elenco Dynamic Routes Dynamic Route Sorgente Nome del Dynamic Route Dynamic Route Variabli Dynamic Route: %s Dynamic Route: %s / Option: %s Modifica Dynamc Route:  Modifica: Abilita cattura DTMF Registrazione da riprodurre all'inizio del Dynamic Route. Nome host del server MySQl Destinazione invalida Registrazione per invalido Tentativi Registrazione per ripetere su invalido Elenco Dynamic Routes Valore Numero massimo di cifre dtmf Numero massimo di cifre DTMF. Se zero, non c'è limite. Evita di dover premere il tasto # per DTMF di lungezza fissa. MySQL database MySQL hostname MySQL password MySQL query MySQL username Nome del variabile di ritorno dello script AGI Nome di questo Dynamic Route Variabile per salvare input Variabile per salvare risultato No Astman non disponibile, Dynroute non caricato Nessuno Numero di tentativi ODBC Function Funzione ODBC. Il valore dovrebbe essere un nome di sezione da /etc/asterisk/func_odbc.conf. Quando si esegue "core show functions" dal console di asterisk il nome ha un prefisso di ODBC_ ODBC query Password MySQL Registrazione per ripetere su invalido Registrazione per input invalido Query da eseguire per ottenere le info dal database MySQL. Si possono utilizzare queste sostituzioni<br>[NUMBER] callerid<br>[INPUT] dtmf<br>[DID] numero chiamato<br>[xxx] dove xxx è il nome di un variabile di input o di risultato salvato da un precedente dynamic route utilizzato sulla stessa chiamata. Query da eseguire per ottenere le info dal database. Si possono utilizzare queste sostituzioni<br>[NUMBER] callerid<br>[INPUT] dtmf<br>[DID] numero chiamato<br>[xxx] dove xxx è il nome di un variabile di input o di risultato salvato da un precedente dynamic route utilizzato sulla stessa chiamata. Reimposta Variabile per salvare input Variabile per salvare risultato Tipo sorgente Invio Sorgente delle info. Timeout URL Lookup URL da usare per ottenere il risultato (deve ritornare solo testo, no html, xml o json). Esempio http://localhost/test.php?param1=4&param2=9. Si possono utilizzare queste sostituzioni<br>[NUMBER] callerid<br>[INPUT] dtmf<br>[DID] numero chiamato<br>[xxx] dove xxx è il nome di un variabile di input o di risultato salvato da un precedente dynamic route utilizzato sulla stessa chiamata. Utente MySQL Validazione Regola di validazione con un'espressione REGEX_MATCH. Ad esempio per controllare se l'input è fra 3 e 4 cifre: ^[0-9]\{3,4\}$ Si valore da confrontare 