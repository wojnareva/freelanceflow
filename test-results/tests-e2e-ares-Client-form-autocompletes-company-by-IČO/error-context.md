# Page snapshot

```yaml
- generic [ref=e2]:
  - link "FreelanceFlow" [ref=e4] [cursor=pointer]:
    - /url: /
    - generic [ref=e5] [cursor=pointer]:
      - img [ref=e6] [cursor=pointer]
      - text: FreelanceFlow
  - generic [ref=e9]:
    - generic [ref=e10]:
      - generic [ref=e11]: Email
      - textbox "Email" [active] [ref=e12]
    - generic [ref=e13]:
      - generic [ref=e14]: Password
      - textbox "Password" [ref=e15]
    - generic [ref=e17]:
      - checkbox "Remember me" [ref=e18]
      - generic [ref=e19]: Remember me
    - generic [ref=e20]:
      - link "Forgot your password?" [ref=e21] [cursor=pointer]:
        - /url: http://127.0.0.1:8000/forgot-password
      - button "Log in" [ref=e22]
```