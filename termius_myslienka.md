### **Popis Architektúry a Pracovného Postupu v Termiuse (Stav k 24.6.2025)**

Aktuálne mám v aplikácii Termius nakonfigurovaný pokročilý systém siedmich (7) hostov pre prístup na môj Hetzner VPS (CX22, IP: 37.47.137.100). Tento systém som si navrhol pre maximálnu efektivitu, paralelizáciu práce a jasné oddelenie úloh, najmä pri práci na cestách z mobilu, kde je rýchlosť a priamočiarosť kľúčová.

Môj systém je teraz logicky rozdelený do skupín (Groups) priamo v Termiuse, čo zaisťuje perfektnú prehľadnosť.

---

### **Skupina 1: Perzistentné "Tmux Session"**

Táto skupina obsahuje tri trvalo bežiace session, ktoré sú srdcom mojej 24/7 dostupnosti. Umožňujú mi začať prácu na PC a plynule v nej pokračovať na mobile alebo naopak, bez straty kontextu.

*   **Host 1: `01_CC_tmux_root_user`**
    *   **Snippet:** `tmux attach -t claude`
    *   **Účel:** Toto je moja hlavná, "master" session. Prebieha v nej kľúčová a najdôležitejšia práca s Claude Code.

*   **Host 2: `02_CC_tmux_root_user`**
    *   **Snippet:** `tmux attach -t claude_2_tmux`
    *   **Účel:** Sekundárna session, ktorú používam na pomocné alebo menej podstatné úlohy, aby som si "nezašpinil" kontext v hlavnej master session. Ideálna na vedľajšie experimenty alebo analýzy.

*   **Host 3: `03_CC_tmux_root_user`**
    *   **Snippet:** `tmux attach -t claude_3_tmux_ccusage`
    *   **Účel:** Toto je nová, špecializovaná **monitoringová session**. Po pripojení sa okamžite spustí nástroj `ccusage`, ktorý mi v reálnom čase ukazuje dashboard so spotrebou tokenov, nákladmi a ďalšími metrikami Claude Code. Je to môj "kontrolný panel" pre správu nákladov.

*   **Logika pomenovania v tejto skupine:**
    *   `01, 02, 03`: Poradové číslo a priorita session.
    *   `CC`: Skratka pre Claude Code.
    *   `tmux`: Označuje, že ide o perzistentnú `tmux` session.
    *   `root_user`: Znamená, že session beží pod `root` používateľom.

---

### **Skupina 2: Dočasné "Priame Session"**

Táto skupina obsahuje tri dočasné session, ktoré používam na prácu, o ktorej viem, že ju začnem a dokončím v rámci jedného pripojenia. Sú ideálne na rýchle úlohy a ich hlavnou silou je možnosť **paralelizácie** – často mám spustené všetky tri naraz a pracujem na rôznych úlohách súčasne.

*   **Host 1: `01_CC_NO_DSP_root_user`** (Snippet: `claude`)
*   **Host 2: `02_CC_NO_DSP_root_user`** (Snippet: `claude`)
*   **Host 3: `03_CC_NO_DSP_root_user`** (Snippet: `claude`)

*   **Logika pomenovania v tejto skupine:**
    *   `NO_DSP`: Kľúčová informácia. Znamená, že Claude sa spúšťa bez parametra `--dangerously-skip-permissions`. Zistil som, že tento parameter nie je možné použiť pri spustení pod `root` používateľom, pravdepodobne z bezpečnostných dôvodov.

---

### **Samostatný Host: Testovacia & Bezpečnostná Session**

Tento host je mimo hlavných skupín, pretože slúži na špecifický účel – experimentovanie a prácu v bezpečnejšom prostredí.

*   **Host: `CC_DSP_NO_root_user_Jozef`**
    *   **Snippet:** `claude --dangerously-skip-permissions`
    *   **Účel:** Táto session vznikla za účelom otestovania parametra `--dangerously-skip-permissions`.
    *   **Kľúčový rozdiel:** Ako jediná nebeží pod `root` používateľom, ale pod samostatne vytvoreným, **neprivilegovaným používateľom `jozef`**. Toto je môj sandbox na testovanie potenciálne rizikových operácií bez ohrozenia stability celého systému.

---
 
Tento systém viacerých, jasne definovaných hostov, organizovaných do skupín, mi cez aplikáciu Termius umožňuje extrémne rýchly, prehľadný a paralelný prístup k môjmu VPS. Je to na mieru šitý workflow, ktorý presne vyhovuje mojim potrebám.
 
---
 
### **Praktické nastavenie na PC vs. mobil**
 
#### **Desktopová aplikácia Termius (Windows / macOS / Linux)**
1. **Prihlásenie a synchronizácia**
   * Prihlás sa do Termius účtu (Settings → Sign In) a zapni **Sync**.
   * Otvor **Settings → Keychain** a prever, či sú tam pripravené kľúče pre prístup na server.
     * Najprv si v termináli over, či už nejaké kľúče existujú: `ls ~/.ssh/*.pub`. Ak sa zobrazia súbory (napr. `id_rsa.pub`, `id_ed25519.pub`), znamená to, že máš pripravené verejné kľúče a môžeš preskočiť na import.
     * Ak zatiaľ žiadne nemáš, vytvor si ich priamo na počítači:
       1. Otvor terminál a zadaj `ssh-keygen -t ed25519 -C "hetzner-root"` (odporúčaný moderný formát). Ako názov súboru zvoľ napr. `~/.ssh/hetzner-root`, prípadne nechaj predvolený.
       2. Pri výzve na passphrase si nastav bezpečné heslo (alebo stlač Enter, ak ho nechceš používať).
       3. Rovnakým spôsobom si môžeš vytvoriť aj druhý kľúč pre používateľa `jozef`, napr. `ssh-keygen -t ed25519 -C "hetzner-jozef"`.
       4. Verejnú časť kľúča skopíruj na server (napr. `ssh-copy-id -i ~/.ssh/hetzner-root.pub root@37.47.137.100` a pre `jozef` `ssh-copy-id -i ~/.ssh/hetzner-jozef.pub jozef@37.47.137.100`). Alternatívne ju vlož ručne do `~/.ssh/authorized_keys` na serveri.
     * Keď už súbor so súkromným kľúčom existuje, v Termius klikni na **Add Key → Import from file**, vyber príslušný `.pem`/bez prípony súbor (napr. `hetzner-root`), zadaj prípadný passphrase a ulož.
     * Pomenuj záznam (napr. `hetzner-root`, `hetzner-jozef`) a stlač **Save**. Neskôr pri každom hostovi stačí v poli *Key* vybrať správny záznam.
2. **Skupiny a hosti**
   * V sekcii *Hosts* vytvor skupiny „Skupina 1 – tmux“ a „Skupina 2 – claude NO_DSP“.
   * Postupne založ hostov podľa tabuľky vyššie a u každého:
     * vyplň IP `37.47.137.100`, port `22`, správne užívateľské meno (`root` alebo `jozef`),
     * priraď zodpovedajúci kľúč z *Keychain*,
     * v *Snippets* vlož požadovaný príkaz (`tmux attach…` alebo `claude …`) a aktivuj „Run snippet on connect“.
3. **Testovanie**
   * Každého hosta otvor kliknutím na **Connect** a over, že prebehne automatický štart tmux / claude.
   * Ak sa tmux session nenačíta, doplň na server fallback skript (`tmux new`) alebo uprav snippet.
4. **Záloha konfigurácie**
   * Po dokončení klikni vľavo dole na ikonu Sync a vyber **Sync now** alebo si urob export (Settings → Export).
 
#### **Mobilná aplikácia Termius (iOS / Android)**
1. **Inštalácia a prihlásenie**
   * Nainštaluj Termius z App Store / Google Play.
   * Prihlás sa do rovnakého účtu a povoľ **Sync** (Settings → Account → Sync).
2. **Kontrola prevzatých hostov**
   * V *Hosts* by sa mali po synchronizácii objaviť všetky skupiny a hostia vytvorení na PC.
   * Otvor náhodného hosta a skontroluj, že sú prítomné snippets aj priradený kľúč.
3. **Nastavenie klávesnice a terminálu**
   * V Settings → Terminal prispôsob klávesové skratky (napr. Escape, Ctrl) pre prácu s tmux.
   * Ak používaš FaceID/biometriu, zapni „Ask for biometric auth“ pre rýchle a bezpečné prihlásenie.
4. **Ručné vytvorenie (ak Sync nie je dostupný)**
   * Postupuj rovnako ako na PC: vytvor skupiny, hostov, priraď IP, port, používateľské meno, kľúče a snippets.
 
#### **Synchronizácia a bežná údržba**
* Po každej väčšej zmene (úprava snippetov, nové tmux session) spusti Sync na PC aj mobile.
* Občas si manuálne otestuj prihlásenie na oboch zariadeniach, nech overíš, že sa neporušilo kľúčové spojenie.
* Pri výmene zariadenia stačí prihlásiť Termius účet a zapnúť Sync – konfigurácia sa natiahne automaticky.
 
---
