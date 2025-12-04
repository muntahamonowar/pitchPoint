# Password Input Test Table - Sign Up Page

## Description of Item to Be Tested: Password input in sign up

**Required Field:** Y

| Test Type | Test Data | Expected Result | Actual Result |
|-----------|-----------|----------------|---------------|
| **Extreme Min** | "" (empty) | Reject | |
| **Min -1** | 7 Characters | Reject | |
| **Min (Boundary)** | 8 Characters | Accept | |
| **Min +1** | 9 characters | Accept | |
| **Max -1** | 254 characters | Accept | |
| **Max (Boundary)** | 255 characters | Accept | |
| **Max +1** | 256 characters | Accept | |
| **Mid** | 16 characters | Accept | |
| **Extreme Max** | 528 characters | Accept | |
| **Invalid data type** | `<script>` | Rejected by firewall | |
| **Other tests** | `DROP TABLE` | Rejected by firewall | |
| **Other tests** | `file:\\/\\/` | Rejected by firewall | |
| **Other tests** | `Cmd =` | Rejected by firewall | |
